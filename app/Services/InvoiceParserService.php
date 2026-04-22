<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class InvoiceParserService
{
    protected $ocrService;
    protected $confidenceThreshold = 0.70;

    public function __construct()
    {
        $this->ocrService = new OCRService();
    }

    public function parse($pdfPath)
    {
        try {
            // Step 1: Try OCRService
            $ocrResult = $this->ocrService->extractInvoiceData($pdfPath);

            if (!$ocrResult['success']) {
                Log::warning('OCR parsing failed for ' . $pdfPath);
                return $this->parseViaRegex($pdfPath);
            }

            $ocrData = $ocrResult['data'];
            $confidence = $ocrData['confidence'] ?? 0;

            // Step 2: Check confidence threshold
            if ($confidence < $this->confidenceThreshold) {
                Log::info('OCR confidence below threshold (' . $confidence . '), falling back to regex');
                
                // Get regex results and merge intelligently
                $regexData = $this->parseViaRegex($pdfPath);
                return $this->mergeResults($ocrData, $regexData);
            }

            // Return OCR result if confidence is good
            return [
                'success' => true,
                'data' => $ocrData,
                'parser_method' => 'ocr',
                'merged' => false
            ];

        } catch (\Exception $e) {
            Log::error('Invoice parsing error: ' . $e->getMessage());
            return $this->parseViaRegex($pdfPath);
        }
    }

    private function parseViaRegex($pdfPath)
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            return [
                'success' => true,
                'data' => $this->extractViaRegex($text),
                'parser_method' => 'regex',
                'merged' => false
            ];

        } catch (\Exception $e) {
            Log::error('Regex parsing failed: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'error' => 'All parsing methods failed'
            ];
        }
    }

    private function extractViaRegex($text)
    {
        return [
            'vendor_name' => $this->extractVendorName($text),
            'gstin' => $this->extractGSTIN($text),
            'invoice_number' => $this->extractInvoiceNumber($text),
            'invoice_date' => $this->extractInvoiceDate($text),
            'amount' => $this->extractAmount($text),
            'tax' => $this->extractTax($text),
            'total' => $this->extractTotal($text),
            'confidence' => 0.60,
            'ocr_method' => 'regex'
        ];
    }

    private function mergeResults($ocrData, $regexResult)
    {
        $regexData = $regexResult['data'];
        $mergedData = [];

        // Define field priority: OCR first, then regex if OCR field is null
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
            // Prefer OCR if available
            if (!empty($ocrData[$field])) {
                $mergedData[$field] = $ocrData[$field];
            }
            // Fallback to regex if OCR missing
            elseif (!empty($regexData[$field])) {
                $mergedData[$field] = $regexData[$field];
            }
            // Default to null
            else {
                $mergedData[$field] = null;
            }
        }

        // Calculate merged confidence
        $ocrConfidence = $ocrData['confidence'] ?? 0;
        $regexConfidence = $regexData['confidence'] ?? 0;
        $mergedConfidence = ($ocrConfidence + $regexConfidence) / 2;

        $mergedData['confidence'] = round($mergedConfidence, 2);
        $mergedData['ocr_method'] = 'merged';

        Log::info('Invoice parsed via merged method. Confidence: ' . $mergedConfidence);

        return [
            'success' => true,
            'data' => $mergedData,
            'parser_method' => 'merged',
            'merged' => true,
            'merge_details' => [
                'ocr_confidence' => $ocrConfidence,
                'regex_confidence' => $regexConfidence,
                'final_confidence' => round($mergedConfidence, 2)
            ]
        ];
    }

    private function extractVendorName($text)
    {
        $patterns = [
            '/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Ship\s+To)/mi',
            '/\b(?:Supplier|Vendor|Bill\s*From|Sold\s*By|From|M\/?S\.?|Messrs)\b\s*[:\-]?\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+Invoice|\s+Date|\s+Address|\s+Phone|\s+Email)/mi',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $candidate = trim(preg_replace('/\s+/', ' ', (string) ($matches[1] ?? '')));
                $candidate = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email)\b/i', $candidate)[0] ?? $candidate;
                $candidate = trim((string) $candidate, " \t\n\r\0\x0B:,-");
                if (strlen($candidate) >= 4 && !preg_match('/^(invoice|tax|total|ship\s*to|bill\s*to)$/i', $candidate)) {
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
}
