<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;
use App\Models\Vendor;
use Smalot\PdfParser\Parser;

class ReprocessInvoicesAdvancedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:reprocess-advanced {--limit=20 : Number of invoices to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Advanced reprocessing with vendor database matching and enhanced patterns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Advanced Invoice Reprocessing Script ===');
        $this->info('Enhanced extraction with vendor database matching.');
        $this->newLine();

        // Get vendor database for matching
        $vendors = Vendor::all()->keyBy(function($vendor) {
            return strtolower($vendor->vendor_name);
        });

        // Get invoices that need reprocessing (low confidence or needs review)
        $limit = $this->option('limit');
        $invoicesToReprocess = PurchaseInvoice::query()
            ->where(function($query) {
                $query->where('confidence_score', '<', 80)
                      ->orWhere('confidence_score', null)
                      ->orWhere('status', 'needs_review')
                      ->orWhere('status', 'draft');
            })
            ->whereNotNull('po_invoice_file')
            ->limit($limit)
            ->get();

        $this->info("Found " . count($invoicesToReprocess) . " invoices to reprocess.");
        $this->info("Vendor database loaded: " . count($vendors) . " vendors for matching.");
        $this->newLine();

        $processedCount = 0;
        $updatedCount = 0;

        foreach ($invoicesToReprocess as $invoice) {
            $processedCount++;
            $this->info("Processing invoice #{$processedCount}: {$invoice->invoice_no}");
            
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
                // Extract text from PDF
                $parser = new Parser();
                $pdf = $parser->parseFile($pdfPath);
                $text = $pdf->getText();
                
                if (empty($text)) {
                    $this->warn("  - Could not extract text from PDF, skipping...");
                    continue;
                }
                
                // Extract data using enhanced patterns
                $extractedData = $this->extractInvoiceDataAdvanced($text, $vendors);
                
                $this->info("  - Advanced PDF extraction successful");
                $this->info("  - Vendor: " . ($extractedData['vendor_name'] ?? 'N/A'));
                $this->info("  - Total: " . ($extractedData['total'] ?? 'N/A'));
                $this->info("  - Confidence: " . ($extractedData['confidence'] ?? 'N/A') . "%");
                $this->info("  - GSTIN: " . ($extractedData['gstin'] ?? 'N/A'));
                
                // Update invoice with improved data
                $updateData = [];
                
                // Update vendor name if better
                if (!empty($extractedData['vendor_name'])) {
                    $updateData['vendor_name'] = $extractedData['vendor_name'];
                    $updateData['vendor_name_raw'] = $extractedData['vendor_name'];
                }
                
                // Update amounts if found
                if (!empty($extractedData['total'])) {
                    $updateData['total_amount'] = $extractedData['total'];
                    $updateData['grand_total'] = $extractedData['total'];
                }
                
                if (!empty($extractedData['amount'])) {
                    $updateData['amount'] = $extractedData['amount'];
                }
                
                // Update other fields
                if (!empty($extractedData['invoice_number'])) {
                    $updateData['invoice_no'] = $extractedData['invoice_number'];
                }
                
                if (!empty($extractedData['invoice_date'])) {
                    $updateData['invoice_date'] = $extractedData['invoice_date'];
                }
                
                if (!empty($extractedData['gstin'])) {
                    $updateData['gstin'] = $extractedData['gstin'];
                    $updateData['vendor_gstin'] = $extractedData['gstin'];
                    $updateData['gst_number'] = $extractedData['gstin'];
                }
                
                // Update confidence score
                $updateData['confidence_score'] = $extractedData['confidence'];
                
                // Update status if confidence is good
                if ($extractedData['confidence'] >= 85) {
                    $updateData['status'] = 'verified';
                } elseif ($extractedData['confidence'] >= 70) {
                    $updateData['status'] = 'needs_review';
                }
                
                // Store raw JSON for debugging
                $rawJson = $invoice->raw_json ?? [];
                $rawJson['reprocessed_advanced'] = [
                    'timestamp' => now()->toDateTimeString(),
                    'old_confidence' => $invoice->confidence_score,
                    'new_confidence' => $extractedData['confidence'],
                    'method' => 'advanced_pdf_parser_with_vendor_matching',
                    'extracted_data' => $extractedData,
                    'vendor_matched' => $extractedData['vendor_matched'] ?? false
                ];
                $updateData['raw_json'] = $rawJson;
                
                if (!empty($updateData)) {
                    $invoice->update($updateData);
                    $updatedCount++;
                    $this->info("  - Invoice updated successfully!");
                } else {
                    $this->warn("  - No improvements found, keeping original data.");
                }
                
            } catch (Exception $e) {
                $this->error("  - Error processing invoice: " . $e->getMessage());
            }
            
            $this->newLine();
        }

        $this->info('=== Advanced Reprocessing Complete ===');
        $this->info("Total invoices processed: {$processedCount}");
        $this->info("Invoices updated: {$updatedCount}");
        $remainingCount = PurchaseInvoice::where('confidence_score', '<', 80)->orWhere('confidence_score', null)->count();
        $this->info("Remaining invoices to process: {$remainingCount}");

        if ($updatedCount > 0) {
            $this->newLine();
            $this->info('Please refresh the Auto Invoice Processing page to see the improvements.');
        }

        return 0;
    }
    
    /**
     * Extract invoice data using advanced patterns and vendor matching
     */
    private function extractInvoiceDataAdvanced($text, $vendors)
    {
        $normalized = preg_replace('/\s+/', ' ', $text) ?? '';
        
        $data = [
            'vendor_name' => $this->extractVendorNameAdvanced($text, $vendors),
            'gstin' => $this->extractGSTIN($text),
            'invoice_number' => $this->extractInvoiceNumber($text),
            'invoice_date' => $this->extractInvoiceDate($text),
            'amount' => $this->extractAmount($text),
            'total' => $this->extractTotal($text),
            'confidence' => 0,
            'vendor_matched' => false,
        ];
        
        // Check if vendor was matched from database
        if (!empty($data['vendor_name'])) {
            $vendorKey = strtolower($data['vendor_name']);
            if ($vendors->has($vendorKey)) {
                $data['vendor_matched'] = true;
                $data['confidence'] += 20; // Boost confidence for database matches
            }
        }
        
        // Calculate confidence based on fields extracted
        $fieldScore = 0;
        $maxFields = 6;
        
        $fields = ['vendor_name', 'gstin', 'invoice_number', 'invoice_date', 'amount', 'total'];
        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                $fieldScore++;
            }
        }
        
        $baseConfidence = ($fieldScore / $maxFields) * 80; // Base confidence up to 80%
        $data['confidence'] = round(min(100, $baseConfidence + ($data['vendor_matched'] ? 20 : 0)), 2);
        
        return $data;
    }
    
    private function extractVendorNameAdvanced($text, $vendors)
    {
        // First try database matching with GSTIN
        $gstin = $this->extractGSTIN($text);
        if ($gstin) {
            $vendorByGstin = $vendors->firstWhere('gstin', $gstin);
            if ($vendorByGstin) {
                return $vendorByGstin->vendor_name;
            }
        }
        
        // Enhanced patterns for vendor name extraction
        $patterns = [
            // Bill To patterns (most reliable)
            '/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{5,140})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Ship\s+To)/mi',
            // Supplier/Vendor patterns
            '/\b(?:Supplier|Vendor|Bill\s*From|Sold\s*By|From|M\/?S\.?|Messrs)\b\s*[:\-]?\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{5,140})(?=\R|\s+GSTIN|\s+Invoice|\s+Date|\s+Address|\s+Phone|\s+Email)/mi',
            // Company name patterns at top of invoice
            '/^([A-Z][A-Z0-9&.,()\s]{5,100})\s*(?:\R|\s)+(?:GSTIN|PAN|TIN|CIN)/mi',
            // Service provider patterns
            '/\b(?:Service\s*Provider|Operator|Provider)\b\s*[:\-]?\s*([A-Z][A-Z0-9&.,()\s]{3,100})/mi',
            // Look for known company names in text
            '/\b(Bharti\s*Airtel|Vodafone\s*Idea|Airtel|Jio|BSNL|Asianet|Sundaram\s*Finance|INFRASPOT)\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $candidate = trim(preg_replace('/\s+/', ' ', (string) ($matches[1] ?? '')));
                $candidate = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email|address|date|total|tax|pan|tin|cin)\b/i', $candidate)[0] ?? $candidate;
                $candidate = trim((string) $candidate, " \t\n\r\0\x0B:,-");
                
                if (strlen($candidate) >= 4 && preg_match('/[A-Za-z]/', $candidate) && !preg_match('/^(invoice|tax|total|ship\s*to|bill\s*to|date|address|phone|email)$/i', $candidate)) {
                    
                    // Try to match with vendor database using fuzzy matching
                    $matchedVendor = $this->matchVendorFuzzy($candidate, $vendors);
                    if ($matchedVendor) {
                        return $matchedVendor->vendor_name;
                    }
                    
                    return $candidate;
                }
            }
        }

        return null;
    }
    
    private function matchVendorFuzzy($candidate, $vendors)
    {
        $candidateLower = strtolower($candidate);
        
        // Try exact match first
        if ($vendors->has($candidateLower)) {
            return $vendors->get($candidateLower);
        }
        
        // Try partial matching
        foreach ($vendors as $vendor) {
            $vendorNameLower = strtolower($vendor->vendor_name);
            
            // Check if candidate contains vendor name or vice versa
            if (strpos($candidateLower, $vendorNameLower) !== false || strpos($vendorNameLower, $candidateLower) !== false) {
                return $vendor;
            }
            
            // Check for significant word overlap
            $candidateWords = explode(' ', $candidateLower);
            $vendorWords = explode(' ', $vendorNameLower);
            
            $commonWords = array_intersect($candidateWords, $vendorWords);
            if (count($commonWords) >= 2 && (count($commonWords) / count($vendorWords)) >= 0.5) {
                return $vendor;
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
        $patterns = [
            '/(?:invoice\s*(?:no|number|#)?|bill\s*(?:no|number)?)[\s:]*([A-Z0-9\-\/]+)/i',
            '/(?:inv|invoice)[\s:#]*([A-Z0-9\-\/]+)/i',
            '/^([A-Z0-9\-\/]{5,30})$/m',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $invoiceNo = trim($matches[1]);
                if (strlen($invoiceNo) >= 3 && preg_match('/[A-Z0-9]/', $invoiceNo)) {
                    return $invoiceNo;
                }
            }
        }
        return null;
    }
    
    private function extractInvoiceDate($text)
    {
        $patterns = [
            '/(?:invoice\s*date|date|bill\s*date)[\s:]*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4}|\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/i',
            '/(?:date|dated)[\s:]*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/i',
            '/(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->normalizeDate($matches[1]);
            }
        }
        return null;
    }
    
    private function extractAmount($text)
    {
        $patterns = [
            '/(?:sub\s*total|subtotal|taxable\s*amt|taxable\s*value)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/(?:amount|before\s*tax)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = (float) str_replace(',', '', $matches[1]);
                if ($amount > 100) {
                    return $amount;
                }
            }
        }
        return null;
    }
    
    private function extractTotal($text)
    {
        // Enhanced patterns for total amounts - prioritize grand total
        $patterns = [
            '/(?:grand\s*total|invoice\s*total|total\s*amount|total\s*payable)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/(?:total\s*due|balance\s*due|amount\s*due)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/\btotal[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
            '/(?:net\s*total|net\s*amount)[\s:]*\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        ];
        
        $extractedAmounts = [];
        
        foreach ($patterns as $priority => $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $amount = (float) str_replace(',', '', $match[1]);
                    if ($amount > 500) {
                        $extractedAmounts[] = [
                            'amount' => $amount,
                            'priority' => $priority,
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
                   ?: \DateTime::createFromFormat('d-m-Y', $date);
            return $parsed ? $parsed->format('Y-m-d') : null;
        } catch (Exception $e) {
            return null;
        }
    }
}
