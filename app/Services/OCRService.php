<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OCRService
{
    protected $confidence;

    public function __construct()
    {
        $this->confidence = 0;
    }

    public function extractInvoiceData($pdfPath)
    {
        if (!file_exists($pdfPath)) {
            return $this->errorResponse('PDF file not found');
        }

        // Try AWS Textract first
        if ($this->canUseTextract()) {
            $result = $this->extractViaTextract($pdfPath);
            if ($result['success']) {
                return $result;
            }
        }

        // Try Google Document AI
        if (env('GOOGLE_DOCUMENT_AI_KEY')) {
            $result = $this->extractViaGoogleDocumentAI($pdfPath);
            if ($result['success']) {
                return $result;
            }
        }

        // Fallback to PDF Parser + Regex
        return $this->extractViaPDFParser($pdfPath);
    }

    private function canUseTextract()
    {
        return env('AWS_ACCESS_KEY_ID') && 
               env('AWS_SECRET_ACCESS_KEY') && 
               class_exists('\Aws\Textract\TextractClient');
    }

    private function extractViaTextract($pdfPath)
    {
        try {
            $textractClient = new \Aws\Textract\TextractClient([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ]
            ]);

            $pdfContent = file_get_contents($pdfPath);

            $result = $textractClient->analyzeDocument([
                'Document' => [
                    'Bytes' => $pdfContent
                ],
                'FeatureTypes' => ['FORMS', 'TABLES']
            ]);

            $blocks = $result['Blocks'];
            $text = $this->extractTextFromBlocks($blocks);
            
            $data = $this->parseInvoiceText($text);
            $data['ocr_method'] = 'aws_textract';
            $data['confidence'] = $this->calculateConfidence($data, 0.85);

            return ['success' => true, 'data' => $data];

        } catch (\Exception $e) {
            Log::warning('Textract extraction failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function extractViaGoogleDocumentAI($pdfPath)
    {
        try {
            $pdfContent = base64_encode(file_get_contents($pdfPath));

            $response = Http::withHeaders([
                'x-goog-api-key' => env('GOOGLE_DOCUMENT_AI_KEY'),
            ])->post('https://documentai.googleapis.com/v1/projects/_/locations/us/processors/invoice-parser:process', [
                'raw_document' => [
                    'mime_type' => 'application/pdf',
                    'content' => $pdfContent,
                ]
            ]);

            if ($response->failed()) {
                return ['success' => false, 'error' => $response->body()];
            }

            $text = $response->json()['document']['text'] ?? '';
            $data = $this->parseInvoiceText($text);
            $data['ocr_method'] = 'google_document_ai';
            $data['confidence'] = $this->calculateConfidence($data, 0.80);

            return ['success' => true, 'data' => $data];

        } catch (\Exception $e) {
            Log::warning('Google Document AI extraction failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function extractViaPDFParser($pdfPath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            $data = $this->parseInvoiceText($text);
            $data['ocr_method'] = 'pdfparser_regex';
            $data['confidence'] = $this->calculateConfidence($data, 0.60);

            return ['success' => true, 'data' => $data];

        } catch (\Exception $e) {
            Log::error('PDF Parser extraction failed: ' . $e->getMessage());
            return $this->errorResponse('All OCR methods failed');
        }
    }

    private function parseInvoiceText($text)
    {
        return [
            'vendor_name' => $this->extractVendorName($text),
            'gstin' => $this->extractGSTIN($text),
            'invoice_number' => $this->extractInvoiceNumber($text),
            'invoice_date' => $this->extractInvoiceDate($text),
            'amount' => $this->extractAmount($text),
            'tax' => $this->extractTax($text),
            'total' => $this->extractTotal($text),
            'raw_text' => $text,
        ];
    }

    private function extractVendorName($text)
    {
        // Enhanced patterns for vendor name extraction
        $patterns = [
            // Bill To patterns (most reliable)
            '/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Ship\s+To)/mi',
            // Supplier/Vendor patterns
            '/\b(?:Supplier|Vendor|Bill\s*From|Sold\s*By|From|M\/?S\.?|Messrs)\b\s*[:\-]?\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+Invoice|\s+Date|\s+Address|\s+Phone|\s+Email)/mi',
            // Company name patterns at top of invoice
            '/^([A-Z][A-Z0-9&.,()\s]{5,100})\s*(?:\R|\s)+(?:GSTIN|PAN|TIN|CIN)/mi',
            // Service provider patterns
            '/\b(?:Service\s*Provider|Operator|Provider)\b\s*[:\-]?\s*([A-Z][A-Z0-9&.,()\s]{3,100})/mi',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $candidate = trim(preg_replace('/\s+/', ' ', (string) ($matches[1] ?? '')));
                
                // Clean up the candidate
                $candidate = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email|address|date|total|tax|pan|tin|cin)\b/i', $candidate)[0] ?? $candidate;
                $candidate = trim((string) $candidate, " \t\n\r\0\x0B:,-");
                
                // Additional filtering for common non-vendor terms
                $excludePatterns = [
                    '/^(invoice|tax|total|ship\s*to|bill\s*to|date|address|phone|email)$/i',
                    '/^(private\s*limited|ltd|pvt\.?\.?ltd\.?|limited)$/i', // Only these words alone
                    '/^[0-9]+(\.[0-9]{2})?$/', // Pure numbers
                ];
                
                $isValid = true;
                foreach ($excludePatterns as $excludePattern) {
                    if (preg_match($excludePattern, $candidate)) {
                        $isValid = false;
                        break;
                    }
                }
                
                // Valid vendor name: reasonable length, contains at least one letter, not excluded
                if ($isValid && strlen($candidate) >= 4 && preg_match('/[A-Za-z]/', $candidate)) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    private function extractGSTIN($text)
    {
        if (preg_match('/\b([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1})\b/', $text, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractInvoiceNumber($text)
    {
        if (preg_match('/(?:invoice\s*(?:no|number|#)?|bill\s*(?:no|number)?)[\s:]*([A-Z0-9\-\/]+)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractInvoiceDate($text)
    {
        if (preg_match('/(?:invoice\s*date|date|bill\s*date)[\s:]*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4}|\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/i', $text, $matches)) {
            return $this->normalizeDate($matches[1]);
        }
        return null;
    }

    private function extractAmount($text)
    {
        // More precise patterns for amount/subtotal
        $patterns = [
            '/(?:sub\s*total|subtotal|taxable\s*amt|taxable\s*value)[\s:]*\u20b9?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/(?:amount|before\s*tax)[\s:]*\u20b9?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = (float) str_replace(',', '', $matches[1]);
                // Filter out very small amounts that are likely line items
                if ($amount > 100) {
                    return $amount;
                }
            }
        }
        return null;
    }

    private function extractTax($text)
    {
        if (preg_match('/(?:gst|tax|sgst|cgst)[\s:]*₹?\s*([0-9,]+\.?[0-9]*)/i', $text, $matches)) {
            return (float) str_replace(',', '', $matches[1]);
        }
        return null;
    }

    private function extractTotal($text)
    {
        // Enhanced patterns for total amounts - prioritize grand total
        $patterns = [
            // Grand Total patterns (highest priority)
            '/(?:grand\s*total|invoice\s*total|total\s*amount|total\s*payable)[\s:]*\u20b9?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            // Total Due patterns
            '/(?:total\s*due|balance\s*due|amount\s*due)[\s:]*\u20b9?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            // Simple Total patterns (lower priority)
            '/\btotal[\s:]*\u20b9?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            // Net Total patterns
            '/(?:net\s*total|net\s*amount)[\s:]*\u20b9?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        ];
        
        $extractedAmounts = [];
        
        foreach ($patterns as $priority => $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $amount = (float) str_replace(',', '', $match[1]);
                    // Filter out very small amounts and line items
                    if ($amount > 500) { // Only consider amounts above 500 as potential totals
                        $extractedAmounts[] = [
                            'amount' => $amount,
                            'priority' => $priority,
                            'pattern' => $pattern
                        ];
                    }
                }
            }
        }
        
        // Sort by priority (lower number = higher priority) and then by amount (highest first)
        usort($extractedAmounts, function($a, $b) {
            if ($a['priority'] !== $b['priority']) {
                return $a['priority'] - $b['priority'];
            }
            return $b['amount'] - $a['amount']; // Prefer higher amount for same priority
        });
        
        return !empty($extractedAmounts) ? $extractedAmounts[0]['amount'] : null;
    }

    private function normalizeDate($date)
    {
        try {
            $date = str_replace(['st', 'nd', 'rd', 'th'], '', $date);
            $parsed = \DateTime::createFromFormat('d/m/Y', $date) 
                   ?: \DateTime::createFromFormat('Y-m-d', $date);
            return $parsed ? $parsed->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function calculateConfidence($data, $baseConfidence)
    {
        $fieldScore = 0;
        $maxFields = 7;

        $fields = [
            'vendor_name',
            'gstin',
            'invoice_number',
            'invoice_date',
            'amount',
            'tax',
            'total'
        ];

        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                $fieldScore++;
            }
        }

        // Boost confidence if GSTIN is valid
        $gstinBoost = ($data['gstin'] && $this->isValidGSTIN($data['gstin'])) ? 0.1 : 0;

        $fieldConfidence = ($fieldScore / $maxFields) * 0.8;
        $totalConfidence = min(1.0, $baseConfidence + $fieldConfidence + $gstinBoost);

        return round($totalConfidence, 2);
    }

    private function isValidGSTIN($gstin)
    {
        return preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $gstin) === 1;
    }

    private function extractTextFromBlocks($blocks)
    {
        $text = '';
        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'LINE') {
                $text .= $block['Text'] . ' ';
            }
        }
        return $text;
    }

    private function errorResponse($message)
    {
        return [
            'success' => false,
            'data' => [
                'vendor_name' => null,
                'gstin' => null,
                'invoice_number' => null,
                'invoice_date' => null,
                'amount' => null,
                'tax' => null,
                'total' => null,
                'confidence' => 0,
                'error' => $message,
                'ocr_method' => 'failed'
            ]
        ];
    }
}

