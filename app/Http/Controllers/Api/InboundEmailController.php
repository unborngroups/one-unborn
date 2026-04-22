<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateInvoiceUpload;
use App\Models\EmailLog;
use App\Models\PurchaseInvoice;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Services\InvoiceUploadSecurityService;
use App\Services\InvoiceParserService;
use App\Services\VendorResolverService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InboundEmailController extends Controller
{
    protected $securityService;
    protected $parserService;
    protected $vendorResolver;
    protected $confidenceThreshold = 70;

    public function __construct(
        InvoiceUploadSecurityService $securityService,
        InvoiceParserService $parserService,
        VendorResolverService $vendorResolver
    ) {
        $this->securityService = $securityService;
        $this->parserService = $parserService;
        $this->vendorResolver = $vendorResolver;
    }

    public function receive(ValidateInvoiceUpload $request)
    {
        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $recipientEmail = $request->input('recipient_email') ?? $request->input('to');

            // Validate upload using security service
            $validation = $this->securityService->validate($file);
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'File validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Find company by invoice receiving email
            $company = $this->findCompanyByEmail($recipientEmail);
            if (!$company) {
                Log::warning("No company found for email: {$recipientEmail}");
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found for this email address',
                    'error_code' => 'COMPANY_NOT_FOUND'
                ], 404);
            }

            // Generate secure filename and store
            $filename = $this->securityService->generateSecureFilename($file);
            $path = $file->storeAs(
                config('invoice.email.attachment_path'),
                $filename,
                'local'
            );

            if (!$path) {
                throw new \Exception('Failed to store invoice file');
            }

            // Get file hash for duplicate detection
            $fileHash = $this->securityService->getFileHash($file);

            // Create email log
            $email = EmailLog::create([
                'company_id' => $company->id,
                'sender' => $request->input('sender', 'webhook@system'),
                'subject' => $request->input('subject', 'Invoice Upload'),
                'body' => $request->input('body', ''),
                'attachment_path' => $path,
                'file_hash' => $fileHash,
                'source' => $request->input('source', 'manual'),
                'status' => 'pending'
            ]);

            // Parse invoice PDF to extract data
            $pdfPath = Storage::path($path);
            $parseResult = $this->parserService->parse($pdfPath);

            if (!$parseResult['success']) {
                $this->updateEmailStatus($email, 'parse_failed', 'Invoice parsing failed');
                DB::commit();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse invoice file',
                    'email_id' => $email->id
                ], 400);
            }

            $invoiceData = $parseResult['data'];
            $parserConfidence = $this->normalizeConfidence($invoiceData['confidence'] ?? 0);

            // Resolve vendor
            $matchResult = $this->vendorResolver->resolveMatch($invoiceData);
            $vendor = $matchResult['vendor'];
            $confidence = $this->combineConfidence($parserConfidence, $matchResult['score']);

            // Check for duplicates
            $gstin = $invoiceData['gstin'] ?? null;
            $invoiceNo = $invoiceData['invoice_number'] ?? null;
            $vendorName = $invoiceData['vendor_name'] ?? null;

            if ($this->isDuplicate($gstin, $invoiceNo, $vendorName, $company->id)) {
                Log::info('Duplicate invoice detected: ' . $invoiceNo);
                $status = 'duplicate';
            } else {
                // Determine status based on vendor resolution and confidence
                if (!$vendor || $confidence < $this->confidenceThreshold) {
                    $status = 'needs_review';
                } else {
                    $status = 'draft';
                }
            }

            // Auto-create PurchaseInvoice
            $purchaseInvoice = PurchaseInvoice::create([
                'company_id' => $company->id,
                'vendor_id' => $vendor ? $vendor->id : null,
                'vendor_name' => $vendorName ?: ($vendor?->vendor_name ?? 'Unknown'),
                'vendor_name_raw' => $vendorName,
                'vendor_gstin' => $gstin,
                'gst_number' => $gstin,
                'gstin' => $gstin,
                'invoice_no' => $invoiceNo,
                'invoice_date' => $invoiceData['invoice_date'] ?? null,
                'amount' => $invoiceData['amount'] ?? null,
                'tax_amount' => $invoiceData['tax'] ?? null,
                'grand_total' => $invoiceData['total'] ?? null,
                'raw_json' => $this->withMatchDetails($invoiceData, $matchResult, $parserConfidence, $confidence),
                'confidence_score' => $confidence,
                'po_invoice_file' => $path,
                'created_by' => null,
                'status' => $status
            ]);

            // Record vendor learning if vendor resolved
            if ($vendor) {
                $this->vendorResolver->recordLearning(
                    $vendorName,
                    $gstin,
                    $vendor->id,
                    $confidence,
                    false
                );
            }

            // Update email status
            $this->updateEmailStatus($email, 'processed');

            DB::commit();

            Log::info("Invoice created successfully: {$purchaseInvoice->id} | Status: {$status} | Company: {$company->id} | Confidence: {$confidence}");

            return response()->json([
                'success' => true,
                'message' => 'Invoice received and processed',
                'data' => [
                    'email_id' => $email->id,
                    'purchase_invoice_id' => $purchaseInvoice->id,
                    'status' => $purchaseInvoice->status,
                    'confidence' => $confidence,
                    'vendor_matched' => $vendor ? true : false,
                    'requires_review' => $status === 'needs_review'
                ]
            ], 202);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Invoice reception failed: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process invoice',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Find company by invoice receiving email
     */
    private function findCompanyByEmail($recipientEmail)
    {
        $normalizedRecipient = $this->normalizeEmail($recipientEmail);

        if (!$normalizedRecipient) {
            return null;
        }

        $settings = CompanySetting::with('company')->get();

        foreach ($settings as $setting) {
            if ($this->settingMatchesRecipient($setting, $normalizedRecipient)) {
                return $setting->company;
            }
        }

        return null;
    }

    private function settingMatchesRecipient(CompanySetting $setting, string $recipientEmail): bool
    {
        $configuredEmails = array_filter(array_merge(
            [$this->normalizeEmail($setting->invoice_mail_from_address)],
            $this->extractEmailsFromText($setting->invoice_mail_footer)
        ));

        return in_array($recipientEmail, $configuredEmails, true);
    }

    private function extractEmailsFromText(?string $text): array
    {
        if (!$text) {
            return [];
        }

        preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $matches);

        return array_values(array_unique(array_map(function ($email) {
            return $this->normalizeEmail($email);
        }, $matches[0] ?? [])));
    }

    private function normalizeEmail(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return $email !== '' ? $email : null;
    }

    /**
     * Check for duplicate invoices
     */
    private function isDuplicate($gstin, $invoiceNo, $vendorName, $companyId)
    {
        // Check GSTIN + invoice_no
        if ($gstin && $invoiceNo) {
            if (PurchaseInvoice::where('gstin', $gstin)
                ->where('invoice_no', $invoiceNo)
                ->exists()) {
                return true;
            }
        }

        // Check vendor_name + invoice_no
        if ($vendorName && $invoiceNo) {
            if (PurchaseInvoice::where('vendor_name_raw', $vendorName)
                ->where('invoice_no', $invoiceNo)
                ->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update email status
     */
    private function updateEmailStatus($email, $status, $errorMessage = null)
    {
        $email->update(['status' => $status]);

        if ($errorMessage) {
            Log::warning("Email {$email->id} status: {$status} - {$errorMessage}");
        }
    }

    private function normalizeConfidence($confidence): float
    {
        $numericConfidence = (float) $confidence;
        return $numericConfidence <= 1 ? round($numericConfidence * 100, 2) : round($numericConfidence, 2);
    }

    private function combineConfidence(float $parserConfidence, int $matchScore): float
    {
        if ($matchScore <= 0) {
            return round($parserConfidence * 0.5, 2);
        }

        return round(($parserConfidence * 0.4) + ($matchScore * 0.6), 2);
    }

    private function withMatchDetails(array $invoiceData, array $matchResult, float $parserConfidence, float $combinedConfidence): array
    {
        $invoiceData['matching'] = [
            'parser_confidence' => $parserConfidence,
            'vendor_match_score' => $matchResult['score'],
            'combined_confidence' => $combinedConfidence,
            'matched_by' => $matchResult['matched_by'],
            'gst_match' => $matchResult['gst_match'],
            'name_match' => $matchResult['name_match'],
            'name_similarity' => $matchResult['name_similarity'],
            'vendor_master_name' => $matchResult['vendor_master_name'],
            'vendor_master_display_name' => $matchResult['vendor_master_display_name'],
        ];

        return $invoiceData;
    }
}
