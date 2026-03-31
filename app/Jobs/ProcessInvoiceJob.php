<?php

namespace App\Jobs;

use App\Models\EmailLog;
use App\Models\PurchaseInvoice;
use App\Services\InvoiceParserService;
use App\Services\VendorResolverService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    protected $confidenceThreshold = 70;

    public function __construct(public $emailId) {}

    public function handle()
    {
        DB::beginTransaction();

        try {
            // Step 1: Fetch email
            $email = EmailLog::find($this->emailId);

            if (!$email || !$email->attachment_path) {
                $this->updateEmailStatus($email, 'failed', 'No attachment found');
                DB::rollBack();
                return;
            }

            // Step 2: OCR + parsing
            $pdfPath = Storage::path($email->attachment_path);
            if (!file_exists($pdfPath)) {
                $this->updateEmailStatus($email, 'failed', 'PDF file not found');
                DB::rollBack();
                return;
            }

            $parserService = new InvoiceParserService();
            $parseResult = $parserService->parse($pdfPath);

            if (!$parseResult['success']) {
                $this->updateEmailStatus($email, 'failed', 'Parsing failed');
                DB::rollBack();
                return;
            }

            $invoiceData = $parseResult['data'];

            // Step 3: Vendor resolution (with learning)
            $vendorResolver = new VendorResolverService();
            $matchResult = $vendorResolver->resolveMatch($invoiceData);
            $vendor = $matchResult['vendor'];

            // Step 4: Duplicate detection
            $isDuplicate = $this->isDuplicate(
                $invoiceData['gstin'] ?? null,
                $invoiceData['invoice_number'] ?? null,
                $invoiceData['vendor_name'] ?? null
            );

            // Step 5: If duplicate
            if ($isDuplicate) {
                $this->savePurchaseInvoice(
                    $email,
                    $invoiceData,
                    $vendor,
                    'duplicate',
                    $parseResult['data']['confidence'] ?? 0
                );
                $this->updateEmailStatus($email, 'processed', 'Duplicate invoice detected');
                DB::commit();
                return;
            }

            // Step 6: Determine status
            $parserConfidence = $this->normalizeConfidence($invoiceData['confidence'] ?? 0);
            $confidence = $this->combineConfidence($parserConfidence, $matchResult['score']);
            $isMissingVendor = !$vendor;
            $isBelowThreshold = $confidence < $this->confidenceThreshold;

            $status = ($isMissingVendor || $isBelowThreshold) ? 'needs_review' : 'draft';

            // Step 7: Save invoice
            $purchaseInvoice = $this->savePurchaseInvoice(
                $email,
                $this->withMatchDetails($invoiceData, $matchResult, $parserConfidence, $confidence),
                $vendor,
                $status,
                $confidence
            );

            // Record learning if vendor was resolved
            if ($vendor) {
                $vendorResolver->recordLearning(
                    $invoiceData['vendor_name'] ?? null,
                    $invoiceData['gstin'] ?? null,
                    $vendor->id,
                    $confidence,
                    false
                );
            }

            // Step 8: Update email_logs
            $this->updateEmailStatus($email, 'processed', 'Invoice created: ' . $purchaseInvoice->id);

            DB::commit();

            Log::info('Invoice processed successfully. ID: ' . $purchaseInvoice->id . ', Status: ' . $status);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->updateEmailStatus($email ?? null, 'failed', $e->getMessage());
            Log::error('Invoice processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function isDuplicate($gstin, $invoiceNo, $vendorName)
    {
        // Check GSTIN + invoice_no
        if ($gstin && $invoiceNo) {
            $exists = PurchaseInvoice::where('gstin', $gstin)
                ->where('invoice_no', $invoiceNo)
                ->exists();
            if ($exists) {
                return true;
            }
        }

        // Check vendor_name + invoice_no
        if ($vendorName && $invoiceNo) {
            $exists = PurchaseInvoice::where('vendor_name_raw', $vendorName)
                ->where('invoice_no', $invoiceNo)
                ->exists();
            if ($exists) {
                return true;
            }
        }

        return false;
    }

    private function savePurchaseInvoice($email, $invoiceData, $vendor, $status, $confidence)
    {
        return PurchaseInvoice::create([
            'company_id' => $email->company_id ?? null,
            'vendor_name_raw' => $invoiceData['vendor_name'] ?? null,
            'vendor_name' => $invoiceData['vendor_name'] ?? 'Unknown',
            'gstin' => $invoiceData['gstin'] ?? null,
            'vendor_gstin' => $invoiceData['gstin'] ?? null,
            'gst_number' => $invoiceData['gstin'] ?? null,
            'vendor_id' => $vendor?->id,
            'invoice_no' => $invoiceData['invoice_number'] ?? null,
            'invoice_date' => $invoiceData['invoice_date'] ?? now(),
            'amount' => $invoiceData['amount'] ?? 0,
            'tax_amount' => $invoiceData['tax'] ?? 0,
            'grand_total' => $invoiceData['total'] ?? 0,
            'status' => $status,
            'confidence_score' => $confidence,
            'raw_json' => $invoiceData,
            'po_invoice_file' => $email->attachment_path,
            'created_by' => null,
        ]);
    }

    private function updateEmailStatus($email, $status, $errorMessage = null)
    {
        if (!$email) {
            return;
        }

        $email->update([
            'status' => $status,
            'error_message' => $errorMessage
        ]);
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
