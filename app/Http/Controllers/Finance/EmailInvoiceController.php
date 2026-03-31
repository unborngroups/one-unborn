<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\PurchaseInvoice;
use App\Models\Vendor;
use App\Services\VendorResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class EmailInvoiceController extends Controller
{
    public function receiveEmailWebhook(Request $request)
    {
        try {
            Log::info('📧 Email webhook received', ['payload' => $request->all()]);

            // Normalize common webhook keys from different email providers.
            $payload = $request->all();
            $payload['from'] = $payload['from'] ?? $payload['sender'] ?? null;
            $payload['to'] = $payload['to'] ?? $payload['recipient_email'] ?? $payload['recipient'] ?? null;

            $validated = validator($payload, [
                'from' => 'required|email',
                'to' => 'required|email',
                'subject' => 'nullable|string',
                'body' => 'nullable|string',
                'attachments' => 'nullable|array',
                'attachments.*.filename' => 'required|string',
                'attachments.*.content' => 'required|string', // Base64 encoded
            ])->validate();

            $company = $this->findCompanyByInvoiceEmail($validated['to']);
            if (!$company) {
                Log::warning('⚠️ Invoice webhook recipient is not mapped in company settings', ['to' => $validated['to']]);
                return response()->json([
                    'error' => 'INVALID_RECIPIENT',
                    'message' => 'Recipient email is not configured for invoice intake',
                ], 404);
            }

            $subjectHasInvoiceHint = $this->isInvoiceKeywordText($validated['subject'] ?? '');

            // ✅ Step 1: Check for attachments
            if (empty($validated['attachments'])) {
                Log::warning('⚠️ No attachments in email', ['from' => $validated['from']]);
                return response()->json([
                    'error' => 'NO_ATTACHMENTS',
                    'message' => 'Email has no attachments',
                ], 400);
            }

            $results = [];

            // ✅ Step 2: Process each attachment
            foreach ($validated['attachments'] as $index => $attachment) {
                Log::info('📎 Processing attachment', [
                    'filename' => $attachment['filename'],
                    'index' => $index
                ]);

                // Check if PDF or Image
                $isPdfOrImage = preg_match('/\.(pdf|jpg|jpeg|png)$/i', $attachment['filename']);
                
                if (!$isPdfOrImage) {
                    Log::info('⏭️ Skipping non-PDF/Image: ' . $attachment['filename']);
                    continue;
                }

                $fileHasInvoiceHint = $this->isInvoiceKeywordText($attachment['filename']);

                // ✅ Step 3: Decode Base64
                $fileContent = base64_decode($attachment['content'], true);
                
                if (!$fileContent || strlen($fileContent) < 100) {
                    Log::error('❌ Invalid/Empty file content for: ' . $attachment['filename']);
                    $results[] = [
                        'filename' => $attachment['filename'],
                        'status' => 'FAILED',
                        'reason' => 'INVALID_FILE_CONTENT',
                    ];
                    continue;
                }

                // ✅ Step 4: Save temporarily
                $tempDir = storage_path('temp');
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                $tempPath = $tempDir . '/' . time() . '_' . basename($attachment['filename']);
                file_put_contents($tempPath, $fileContent);

                Log::info('✅ File saved to: ' . $tempPath);

                // ✅ Step 5: Parse Invoice with OCR
                $invoiceData = $this->parseInvoiceFromFile($tempPath);

                if (isset($invoiceData['error'])) {
                    if (!$subjectHasInvoiceHint && !$fileHasInvoiceHint) {
                        Log::info('⏭️ Skipping non-invoice attachment after parse failure', ['filename' => $attachment['filename']]);
                        $results[] = [
                            'filename' => $attachment['filename'],
                            'status' => 'SKIPPED',
                            'reason' => 'NON_INVOICE_ATTACHMENT',
                        ];
                        @unlink($tempPath);
                        continue;
                    }

                    Log::error('❌ OCR Parse Error', $invoiceData);
                    $results[] = [
                        'filename' => $attachment['filename'],
                        'status' => 'FAILED',
                        'reason' => $invoiceData['error'],
                        'message' => $invoiceData['message'] ?? 'Unknown error',
                    ];
                    @unlink($tempPath);
                    continue;
                }

                Log::info('✅ OCR Parse Successful', $invoiceData);

                if (!$subjectHasInvoiceHint && !$fileHasInvoiceHint && empty($invoiceData['is_invoice'])) {
                    Log::info('⏭️ Skipping attachment because it does not look like an invoice', ['filename' => $attachment['filename']]);
                    $results[] = [
                        'filename' => $attachment['filename'],
                        'status' => 'SKIPPED',
                        'reason' => 'NON_INVOICE_ATTACHMENT',
                    ];
                    @unlink($tempPath);
                    continue;
                }

                // ✅ Step 6: Find Vendor
                $resolver = app(VendorResolverService::class);
                $matchResult = $resolver->resolveMatch([
                    'gstin' => $invoiceData['gst'] ?? null,
                    'vendor_name' => $invoiceData['vendor_name'] ?? null,
                ]);
                $vendor = $matchResult['vendor'];

                if (!$vendor && !empty($validated['from'])) {
                    $vendor = Vendor::where('contact_person_email', $validated['from'])->first();
                    if ($vendor) {
                        $nameMatch = trim((string) ($invoiceData['vendor_name'] ?? '')) !== ''
                            && strcasecmp(trim((string) $invoiceData['vendor_name']), trim((string) $vendor->vendor_name)) === 0;
                        $matchResult = [
                            'vendor' => $vendor,
                            'score' => $nameMatch ? 78 : 72,
                            'matched_by' => 'email',
                            'gst_match' => !empty($invoiceData['gst']) && strtoupper((string) $invoiceData['gst']) === strtoupper((string) $vendor->gstin),
                            'name_match' => $nameMatch,
                            'name_similarity' => $nameMatch ? 100 : 0,
                            'vendor_master_name' => $vendor->vendor_name,
                            'vendor_master_display_name' => $vendor->business_display_name,
                        ];
                        Log::info('✅ Vendor found by email', ['vendor_id' => $vendor->id, 'email' => $validated['from']]);
                    }
                }

                if (!$vendor) {
                    Log::warning('⚠ Vendor not found; creating unmapped invoice for review', [
                        'gst' => $invoiceData['gst'] ?? 'Not found',
                        'email' => $validated['from'],
                    ]);
                }

                // ✅ Step 7: Save file permanently
                $fileName = time() . '_' . basename($attachment['filename']);
                $publicPath = public_path('images/poinvoice_files/' . $fileName);
                
                // Ensure directory exists
                if (!is_dir(dirname($publicPath))) {
                    mkdir(dirname($publicPath), 0755, true);
                }
                
                copy($tempPath, $publicPath);
                Log::info('✅ File saved to public: ' . $publicPath);

                // ✅ Step 8: Create Purchase Invoice
                try {
                    $totalAmount = $invoiceData['arc'] + $invoiceData['otc'] + $invoiceData['static'];
                    
                    if ($totalAmount <= 0) {
                        $totalAmount = $invoiceData['router'] > 0 ? $invoiceData['router'] : 1;
                    }

                    $extractedVendorName = $invoiceData['vendor_name'] ?? null;
                    $vendorName = $extractedVendorName ?? $vendor?->vendor_name;
                    $status = $vendor ? 'email_imported' : 'needs_review';
                    $parserConfidence = $this->calculateParserConfidence($invoiceData);
                    $confidence = $this->combineConfidence($parserConfidence, $matchResult['score'] ?? 0);

                    $invoice = PurchaseInvoice::create([
                        'company_id' => $company->id,
                        'type' => 'purchase',
                        'vendor_id' => $vendor?->id,
                        'vendor_name' => $vendorName ?? 'Unknown',
                        'vendor_name_raw' => $extractedVendorName,
                        'invoice_no' => $invoiceData['invoice_number'] ?? 'AUTO-' . time(),
                        'invoice_date' => now(),
                        'amount' => $totalAmount,
                        'grand_total' => $totalAmount,
                        'total_amount' => $totalAmount,
                        'po_invoice_file' => $fileName,
                        'status' => $status,
                        'arc_amount' => $invoiceData['arc'],
                        'otc_amount' => $invoiceData['otc'],
                        'static_amount' => $invoiceData['static'],
                        'gstin' => $invoiceData['gst'],
                        'gst_number' => $invoiceData['gst'],
                        'vendor_gstin' => $invoiceData['gst'],
                        'confidence_score' => $confidence,
                        'raw_json' => $this->withMatchDetails($invoiceData, $matchResult, $parserConfidence, $confidence),
                    ]);

                    Log::info('✅ Invoice created', ['invoice_id' => $invoice->id]);

                    $results[] = [
                        'filename' => $attachment['filename'],
                        'status' => 'SUCCESS',
                        'invoice_id' => $invoice->id,
                        'vendor_name' => $vendor?->vendor_name,
                        'vendor_mapped' => (bool) $vendor,
                        'total_amount' => $totalAmount,
                        'extracted_data' => $invoiceData,
                    ];

                } catch (\Exception $e) {
                    Log::error('❌ Failed to create invoice', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $results[] = [
                        'filename' => $attachment['filename'],
                        'status' => 'FAILED',
                        'reason' => 'DATABASE_ERROR',
                        'message' => $e->getMessage(),
                    ];
                }

                // Cleanup
                @unlink($tempPath);
            }

            // ✅ Final response
            $successCount = count(array_filter($results, fn($r) => $r['status'] === 'SUCCESS'));

            return response()->json([
                'success' => $successCount > 0,
                'total_processed' => count($results),
                'success_count' => $successCount,
                'results' => $results,
            ], $successCount > 0 ? 201 : 400);

        } catch (\Exception $e) {
            Log::error('❌ Email webhook critical error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'WEBHOOK_ERROR',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🧪 TEST WEBHOOK - Debug endpoint to test without email
     */
    public function testWebhook(Request $request)
    {
        Log::info('🧪 TEST webhook called');

        return response()->json([
            'success' => true,
            'message' => 'Webhook is working! ✅',
            'timestamp' => now(),
            'url' => route('webhook.email.invoice'),
            'test_data' => [
                'from' => 'vendor@airtel.com',
                'to' => 'invoices@unborn.co.in',
                'subject' => 'Invoice Test',
                'attachments' => [
                    [
                        'filename' => 'test.pdf',
                        'content' => '[base64_encoded_pdf_here]'
                    ]
                ]
            ]
        ]);
    }

    /**
     * 🔧 Parse Invoice from Local File
     */
    private function parseInvoiceFromFile($filePath)
    {
        try {
            // Prefer OCR when available.
            if (empty(env('OCR_API_KEY'))) {
                return $this->parseViaLocalPdf($filePath);
            }

            $response = Http::attach(
                'file',
                file_get_contents($filePath),
                basename($filePath)
            )->post('https://api.ocr.space/parse/image', [
                'apikey' => env('OCR_API_KEY'),
                'language' => 'eng',
            ]);

            $result = $response->json();

            if (!empty($result['IsErroredOnProcessing']) && $result['IsErroredOnProcessing']) {
                return $this->parseViaLocalPdf($filePath);
            }

            $text = $result['ParsedResults'][0]['ParsedText'] ?? '';

            if (!$text) {
                return $this->parseViaLocalPdf($filePath);
            }

            return [
                'arc' => $this->extractAmount($text, 'ARC'),
                'otc' => $this->extractAmount($text, 'OTC'),
                'static' => $this->extractAmount($text, 'Static'),
                'router' => $this->extractAmount($text, 'Router'),
                'gst' => $this->extractGSTNumber($text),
                'vendor_name' => $this->extractVendorName($text),
                'invoice_number' => $this->extractInvoiceNumber($text),
                'is_invoice' => $this->isInvoiceDocument($text),
            ];

        } catch (\Exception $e) {
            return $this->parseViaLocalPdf($filePath);
        }
    }

    private function parseViaLocalPdf($filePath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            if (!$text) {
                return ['error' => 'NO_TEXT_EXTRACTED'];
            }

            return [
                'arc' => $this->extractAmount($text, 'ARC'),
                'otc' => $this->extractAmount($text, 'OTC'),
                'static' => $this->extractAmount($text, 'Static'),
                'router' => $this->extractAmount($text, 'Router'),
                'gst' => $this->extractGSTNumber($text),
                'vendor_name' => $this->extractVendorName($text),
                'invoice_number' => $this->extractInvoiceNumber($text),
                'is_invoice' => $this->isInvoiceDocument($text),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'PARSE_ERROR',
                'message' => $e->getMessage(),
            ];
        }
    }

    // 🔧 Extract Amount
    private function extractAmount($text, $keyword)
    {
        $pattern = '/' . preg_quote($keyword, '/') . '[\s:₹]*([\d,]+(\.\d+)?)/i';
        preg_match($pattern, $text, $matches);
        return isset($matches[1]) ? (float) str_replace(',', '', $matches[1]) : 0;
    }

    // 🔧 Extract GST Number
    private function extractGSTNumber($text)
    {
        $pattern = '/\b\d{2}[A-Z]{5}\d{4}[A-Z1-9][A-Z][0-9]\d\b/';
        preg_match($pattern, $text, $matches);
        return isset($matches[0]) ? strtoupper($matches[0]) : null;
    }

    // 🔧 Extract Invoice Number
    private function extractInvoiceNumber($text)
    {
        $patterns = [
            '/[Ii]nvoice\s+(?:[Nn]o\.?|[Nn]umber)?\s*:?\s*([A-Z0-9\-\/]+)/i',
            '/[Ii]nv[oice]*\s*[#-]?\s*([A-Z0-9\-\/]+)/i',
        ];

        foreach ($patterns as $pattern) {
            preg_match($pattern, $text, $matches);
            if (isset($matches[1])) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    private function extractVendorName($text)
    {
        $patterns = [
            '/(?:vendor|supplier|from|bill\s*from)\s*[:\-]?\s*([A-Z][A-Z0-9&.,()\-\s]{3,120})/i',
            '/(?:m\/?s\.?|messrs)\s*([A-Z][A-Z0-9&.,()\-\s]{3,120})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $candidate = trim(preg_replace('/\s+/', ' ', $matches[1]));
                $candidate = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email)\b/i', $candidate)[0] ?? $candidate;
                $candidate = trim($candidate, " \t\n\r\0\x0B:,-");
                if (strlen($candidate) >= 4) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    private function calculateParserConfidence(array $invoiceData): float
    {
        $fields = ['gst', 'vendor_name', 'invoice_number'];
        $presentFields = 0;

        foreach ($fields as $field) {
            if (!empty($invoiceData[$field])) {
                $presentFields++;
            }
        }

        return round(($presentFields / count($fields)) * 100, 2);
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
            'vendor_match_score' => $matchResult['score'] ?? 0,
            'combined_confidence' => $combinedConfidence,
            'matched_by' => $matchResult['matched_by'] ?? null,
            'gst_match' => $matchResult['gst_match'] ?? false,
            'name_match' => $matchResult['name_match'] ?? false,
            'name_similarity' => $matchResult['name_similarity'] ?? 0,
            'vendor_master_name' => $matchResult['vendor_master_name'] ?? null,
            'vendor_master_display_name' => $matchResult['vendor_master_display_name'] ?? null,
        ];

        return $invoiceData;
    }

    private function findCompanyByInvoiceEmail(string $recipientEmail)
    {
        $normalizedRecipient = $this->normalizeEmail($recipientEmail);

        if (!$normalizedRecipient) {
            return null;
        }

        $settings = CompanySetting::with('company')->get();

        foreach ($settings as $setting) {
            $configuredEmails = array_filter(array_merge(
                [$this->normalizeEmail($setting->invoice_mail_from_address)],
                $this->extractEmailsFromText($setting->invoice_mail_footer)
            ));

            if (in_array($normalizedRecipient, $configuredEmails, true)) {
                return $setting->company;
            }
        }

        return null;
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

    private function isInvoiceKeywordText(string $text): bool
    {
        $text = strtolower($text);

        foreach (['invoice', 'tax invoice', 'bill', 'gst', 'gstin', 'debit note', 'credit note', 'proforma'] as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function isInvoiceDocument(string $text): bool
    {
        $text = strtolower($text);
        $signals = 0;

        foreach ([
            '/\binvoice\b/',
            '/\bgstin\b|\bgst\b/',
            '/\bcgst\b|\bsgst\b|\bigst\b/',
            '/\binvoice\s*no\b|\binvoice\s*number\b/',
            '/\btotal\b|\bgrand\s*total\b/',
            '/\bbill\s*to\b|\bship\s*to\b/',
        ] as $pattern) {
            if (preg_match($pattern, $text)) {
                $signals++;
            }
        }

        return $signals >= 2;
    }
}
