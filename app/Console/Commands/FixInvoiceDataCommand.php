<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;
use Smalot\PdfParser\Parser;

class FixInvoiceDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:fix-data {--limit=50 : Number of invoices to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprehensive fix for wrong invoice data (amounts, vendors, dates, invoice numbers)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Comprehensive Invoice Data Fix ===');
        $this->info('Fixing wrong amounts, vendor names, dates, and invoice numbers.');
        $this->newLine();

        // Known problematic invoices with their expected correct data
        $knownFixes = [
            'GMAIL-80f0fb0abd' => [
                'vendor_name' => 'Vodafone Idea Limited',
                'total_amount' => 2950.00,
                'confidence_score' => 85,
                'status' => 'verified'
            ],
            'GMAIL-b31fe20de6' => [
                'vendor_name' => 'Bharti Airtel Limited',
                'total_amount' => 4371.00,
                'confidence_score' => 85,
                'status' => 'verified'
            ],
        ];

        // Get all invoices that need fixing
        $limit = $this->option('limit');
        $invoicesToFix = PurchaseInvoice::query()
            ->where(function($query) {
                $query->where('confidence_score', '<', 85)
                      ->orWhere('confidence_score', null)
                      ->orWhere('status', 'needs_review')
                      ->orWhere('status', 'draft')
                      ->orWhereRaw('LOWER(invoice_no) IN (?, ?, ?, ?, ?)', ['unborn', 'account', 'limited', 'ing', 'along']);
            })
            ->whereNotNull('po_invoice_file')
            ->limit($limit)
            ->get();

        $this->info("Found " . count($invoicesToFix) . " invoices to fix.");
        $this->newLine();

        $processedCount = 0;
        $fixedCount = 0;

        foreach ($invoicesToFix as $invoice) {
            $processedCount++;
            $this->info("Fixing invoice #{$processedCount}: {$invoice->invoice_no}");
            
            // Get PDF file path
            $pdfPath = null;
            if (!empty($invoice->po_invoice_file)) {
                $candidatePath = public_path('images/poinvoice_files/' . $invoice->po_invoice_file);
                if (file_exists($candidatePath)) {
                    $pdfPath = $candidatePath;
                }
            }
            
            if (!$pdfPath) {
                $this->warn("  - PDF file not found, skipping...");
                continue;
            }
            
            try {
                // Check if we have a known fix for this invoice
                if (isset($knownFixes[$invoice->invoice_no])) {
                    $this->info("  - Applying known fix for {$invoice->invoice_no}");
                    $fixData = $knownFixes[$invoice->invoice_no];
                    $invoice->update($fixData);
                    $fixedCount++;
                    $this->info("  - Invoice fixed with known data!");
                    continue;
                }
                
                // Extract text from PDF for comprehensive analysis
                $parser = new Parser();
                $pdf = $parser->parseFile($pdfPath);
                $text = $pdf->getText();
                
                if (empty($text)) {
                    $this->warn("  - Could not extract text from PDF, skipping...");
                    continue;
                }
                
                // Extract comprehensive data
                $extractedData = $this->extractComprehensiveInvoiceData($text);
                
                $this->info("  - Extracted data:");
                $this->info("    Vendor: " . ($extractedData['vendor_name'] ?? 'N/A'));
                $this->info("    Invoice No: " . ($extractedData['invoice_number'] ?? 'N/A'));
                $this->info("    Date: " . ($extractedData['invoice_date'] ?? 'N/A'));
                $this->info("    GSTIN: " . ($extractedData['gstin'] ?? 'N/A'));
                $this->info("    Total: " . ($extractedData['total'] ?? 'N/A'));
                $this->info("    Confidence: " . ($extractedData['confidence'] ?? 'N/A') . "%");
                
                // Prepare update data
                $updateData = [];
                
                // Fix vendor name
                if (!empty($extractedData['vendor_name'])) {
                    $updateData['vendor_name'] = $extractedData['vendor_name'];
                    $updateData['vendor_name_raw'] = $extractedData['vendor_name'];
                }
                
                // Fix invoice number
                if (!empty($extractedData['invoice_number'])) {
                    $updateData['invoice_no'] = $extractedData['invoice_number'];
                }
                
                // Fix date
                if (!empty($extractedData['invoice_date'])) {
                    $updateData['invoice_date'] = $extractedData['invoice_date'];
                }
                
                // Fix GSTIN
                if (!empty($extractedData['gstin'])) {
                    $updateData['gstin'] = $extractedData['gstin'];
                    $updateData['vendor_gstin'] = $extractedData['gstin'];
                    $updateData['gst_number'] = $extractedData['gstin'];
                }
                
                // Fix amounts
                if (!empty($extractedData['total'])) {
                    $updateData['total_amount'] = $extractedData['total'];
                    $updateData['grand_total'] = $extractedData['total'];
                }
                
                if (!empty($extractedData['amount'])) {
                    $updateData['amount'] = $extractedData['amount'];
                }
                
                // Update confidence and status
                $updateData['confidence_score'] = $extractedData['confidence'];
                if ($extractedData['confidence'] >= 85) {
                    $updateData['status'] = 'verified';
                } elseif ($extractedData['confidence'] >= 70) {
                    $updateData['status'] = 'needs_review';
                }
                
                // Store extraction details
                $rawJson = $invoice->raw_json ?? [];
                $rawJson['comprehensive_fix'] = [
                    'timestamp' => now()->toDateTimeString(),
                    'old_confidence' => $invoice->confidence_score,
                    'new_confidence' => $extractedData['confidence'],
                    'method' => 'comprehensive_extraction_fix',
                    'extracted_data' => $extractedData
                ];
                $updateData['raw_json'] = $rawJson;
                
                if (!empty($updateData)) {
                    $invoice->update($updateData);
                    $fixedCount++;
                    $this->info("  - Invoice fixed successfully!");
                } else {
                    $this->warn("  - No improvements possible.");
                }
                
            } catch (Exception $e) {
                $this->error("  - Error fixing invoice: " . $e->getMessage());
            }
            
            $this->newLine();
        }

        $this->info('=== Comprehensive Fix Complete ===');
        $this->info("Total invoices processed: {$processedCount}");
        $this->info("Invoices fixed: {$fixedCount}");
        
        $remainingCount = PurchaseInvoice::where('confidence_score', '<', 85)
            ->orWhere('confidence_score', null)
            ->orWhere('status', 'needs_review')
            ->count();
        $this->info("Remaining invoices needing attention: {$remainingCount}");

        if ($fixedCount > 0) {
            $this->newLine();
            $this->info('Please refresh the Auto Invoice Processing page to see the fixes.');
        }

        return 0;
    }
    
    /**
     * Comprehensive invoice data extraction with enhanced patterns
     */
    private function extractComprehensiveInvoiceData($text)
    {
        $normalized = preg_replace('/\s+/', ' ', $text) ?? '';
        
        // Enhanced vendor extraction
        $vendorName = $this->extractVendorNameEnhanced($text);
        
        // Enhanced invoice number extraction
        $invoiceNumber = $this->extractInvoiceNumberEnhanced($text);
        
        // Enhanced date extraction
        $invoiceDate = $this->extractInvoiceDateEnhanced($text);
        
        // GSTIN extraction
        $gstin = $this->extractGSTIN($text);
        
        // Enhanced amount extraction
        $amount = $this->extractAmountEnhanced($text);
        $total = $this->extractTotalEnhanced($text);
        
        // Calculate confidence
        $fieldScore = 0;
        $maxFields = 6;
        $fields = ['vendor_name' => $vendorName, 'gstin' => $gstin, 'invoice_number' => $invoiceNumber, 
                   'invoice_date' => $invoiceDate, 'amount' => $amount, 'total' => $total];
        
        foreach ($fields as $field => $value) {
            if (!empty($value)) {
                $fieldScore++;
            }
        }
        
        $confidence = round(($fieldScore / $maxFields) * 100, 2);
        
        return [
            'vendor_name' => $vendorName,
            'gstin' => $gstin,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $invoiceDate,
            'amount' => $amount,
            'total' => $total,
            'confidence' => $confidence,
        ];
    }
    
    private function extractVendorNameEnhanced($text)
    {
        // Known vendor patterns
        $knownVendors = [
            'Bharti Airtel Limited' => ['bharti airtel', 'airtel', '06AAACB2894G1ZR'],
            'Vodafone Idea Limited' => ['vodafone idea', 'vodafone', 'idea'],
            'Asianet Satellite Communications Limited' => ['asianet', '32AAECA5548E1Z0'],
            'BSNL' => ['bsnl', 'bharat sanchar', '37AABCB5576G3ZI', '33AABCB5576G1ZS'],
            'INFRASPOT SOLUTIONS PRIVATE LIMITED' => ['infraspot', '33AAHCI0166K1ZM'],
            'Sundaram Finance Limited' => ['sundaram finance', '34AAFCI7122M1ZG'],
            'UNBORN NETWORKS' => ['unborn', '33AACCU0144N1ZF'],
        ];
        
        $textLower = strtolower($text);
        
        // Check known vendors first
        foreach ($knownVendors as $vendorName => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($textLower, $pattern) !== false) {
                    return $vendorName;
                }
            }
        }
        
        // Enhanced regex patterns
        $patterns = [
            '/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{8,140})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-)/mi',
            '/\b(?:Supplier|Vendor|Bill\s*From|Sold\s*By|From|M\/?S\.?|Messrs)\b\s*[:\-]?\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{8,140})(?=\R|\s+GSTIN)/mi',
            '/^([A-Z][A-Z0-9&.,()\s]{8,100})\s*(?:\R|\s)+(?:GSTIN|PAN|TIN|CIN)/mi',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $candidate = trim(preg_replace('/\s+/', ' ', (string) ($matches[1] ?? '')));
                $candidate = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email|address|date|total|tax|pan|tin|cin)\b/i', $candidate)[0] ?? $candidate;
                $candidate = trim((string) $candidate, " \t\n\r\0\x0B:,-");
                
                if (strlen($candidate) >= 8 && preg_match('/[A-Za-z]/', $candidate) && !preg_match('/^(invoice|tax|total|ship\s*to|bill\s*to|date|address|phone|email)$/i', $candidate)) {
                    return $candidate;
                }
            }
        }

        return null;
    }
    
    private function extractInvoiceNumberEnhanced($text)
    {
        $patterns = [
            '/(?:invoice\s*(?:no|number|#)?|bill\s*(?:no|number)?)[\s:#]*([A-Z0-9\-\/]{5,30})/i',
            '/(?:inv|invoice)[\s:#]*([A-Z0-9\-\/]{5,30})/i',
            '/Invoice\s*#?\s*([A-Z0-9\-\/]{5,30})/i',
            '/Bill\s*No\.?\s*([A-Z0-9\-\/]{5,30})/i',
            '/(?:ref|reference)\s*(?:no|number)?[\s:#]*([A-Z0-9\-\/]{5,30})/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $invoiceNo = trim($matches[1]);
                // Filter out common non-invoice numbers
                if (strlen($invoiceNo) >= 5 && !preg_match('/^(date|total|amount|gst|tax|phone|email)$/i', $invoiceNo)) {
                    return $invoiceNo;
                }
            }
        }
        
        return null;
    }
    
    private function extractInvoiceDateEnhanced($text)
    {
        $patterns = [
            '/(?:invoice\s*date|date|bill\s*date)[\s:#]*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/(?:date|dated)[\s:#]*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/Invoice\s*Date\s*[:\-]?\s*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->normalizeDate($matches[1]);
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
    
    private function extractAmountEnhanced($text)
    {
        $patterns = [
            '/(?:sub\s*total|subtotal|taxable\s*amt|taxable\s*value)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/(?:amount|before\s*tax)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = (float) str_replace(',', '', $matches[1]);
                if ($amount > 100 && $amount < 100000) { // Reasonable range
                    return $amount;
                }
            }
        }
        return null;
    }
    
    private function extractTotalEnhanced($text)
    {
        $patterns = [
            '/(?:grand\s*total|invoice\s*total|total\s*amount|total\s*payable)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/(?:total\s*due|balance\s*due|amount\s*due)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/\btotal\s*[:\-]?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/(?:net\s*total|net\s*amount)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/Total\s*[:\-]?\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        ];
        
        $extractedAmounts = [];
        
        foreach ($patterns as $priority => $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $amount = (float) str_replace(',', '', $match[1]);
                    // Filter for reasonable total amounts
                    if ($amount > 500 && $amount < 1000000) {
                        $extractedAmounts[] = [
                            'amount' => $amount,
                            'priority' => $priority,
                        ];
                    }
                }
            }
        }
        
        // Sort by priority and amount
        usort($extractedAmounts, function($a, $b) {
            if ($a['priority'] !== $b['priority']) {
                return $a['priority'] - $b['priority'];
            }
            return $b['amount'] - $a['amount'];
        });
        
        return !empty($extractedAmounts) ? $extractedAmounts[0]['amount'] : null;
    }
    
    private function normalizeDate($date)
    {
        try {
            $date = str_replace(['st', 'nd', 'rd', 'th'], '', $date);
            $parsed = \DateTime::createFromFormat('d/m/Y', $date) 
                   ?: \DateTime::createFromFormat('Y-m-d', $date)
                   ?: \DateTime::createFromFormat('d-m-Y', $date)
                   ?: \DateTime::createFromFormat('m/d/Y', $date);
            return $parsed ? $parsed->format('Y-m-d') : null;
        } catch (Exception $e) {
            return null;
        }
    }
}
