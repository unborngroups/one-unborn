<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OCRService;
use App\Services\InvoiceParserService;
use App\Models\PurchaseInvoice;

class ReprocessInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:reprocess {--limit=20 : Number of invoices to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocess existing invoices with improved amount and vendor extraction logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Invoice Reprocessing Script ===');
        $this->info('This will reprocess existing invoices with improved extraction logic.');
        $this->newLine();

        // Initialize services
        $ocrService = new OCRService();
        $invoiceParserService = new InvoiceParserService();

        // Get invoices that need reprocessing (low accuracy or needs review)
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
                // Extract data using improved OCR service
                $ocrResult = $ocrService->extractInvoiceData($pdfPath);
                
                if ($ocrResult['success']) {
                    $extractedData = $ocrResult['data'];
                    
                    $this->info("  - OCR extraction successful");
                    $this->info("  - Vendor: " . ($extractedData['vendor_name'] ?? 'N/A'));
                    $this->info("  - Total: " . ($extractedData['total'] ?? 'N/A'));
                    $this->info("  - Confidence: " . ($extractedData['confidence'] ?? 'N/A') . "%");
                    
                    // Update invoice with improved data
                    $updateData = [];
                    
                    // Update vendor name if better
                    if (!empty($extractedData['vendor_name']) && $extractedData['confidence'] > 70) {
                        $updateData['vendor_name'] = $extractedData['vendor_name'];
                        $updateData['vendor_name_raw'] = $extractedData['vendor_name'];
                    }
                    
                    // Update amounts if better confidence
                    if (!empty($extractedData['total']) && $extractedData['confidence'] > 70) {
                        $updateData['total_amount'] = $extractedData['total'];
                        $updateData['grand_total'] = $extractedData['total'];
                    }
                    
                    if (!empty($extractedData['amount']) && $extractedData['confidence'] > 70) {
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
                    $rawJson['reprocessed'] = [
                        'timestamp' => now()->toDateTimeString(),
                        'old_confidence' => $invoice->confidence_score,
                        'new_confidence' => $extractedData['confidence'],
                        'ocr_method' => $extractedData['ocr_method'] ?? 'unknown',
                        'extracted_data' => $extractedData
                    ];
                    $updateData['raw_json'] = $rawJson;
                    
                    if (!empty($updateData)) {
                        $invoice->update($updateData);
                        $updatedCount++;
                        $this->info("  - Invoice updated successfully!");
                    } else {
                        $this->warn("  - No improvements found, keeping original data.");
                    }
                    
                } else {
                    $this->warn("  - OCR extraction failed: " . ($ocrResult['error'] ?? 'Unknown error'));
                    
                    // Try fallback parser
                    $fallbackResult = $invoiceParserService->parse($pdfPath);
                    if ($fallbackResult['success']) {
                        $extractedData = $fallbackResult['data'];
                        $this->info("  - Fallback parser successful");
                        
                        // Similar update logic for fallback
                        $updateData = [
                            'confidence_score' => $extractedData['confidence'] ?? 60,
                            'raw_json' => array_merge($invoice->raw_json ?? [], [
                                'reprocessed' => [
                                    'timestamp' => now()->toDateTimeString(),
                                    'method' => 'fallback_parser',
                                    'confidence' => $extractedData['confidence'] ?? 60
                                ]
                            ])
                        ];
                        
                        if (!empty($extractedData['vendor_name'])) {
                            $updateData['vendor_name'] = $extractedData['vendor_name'];
                            $updateData['vendor_name_raw'] = $extractedData['vendor_name'];
                        }
                        
                        if (!empty($extractedData['total'])) {
                            $updateData['total_amount'] = $extractedData['total'];
                            $updateData['grand_total'] = $extractedData['total'];
                        }
                        
                        $invoice->update($updateData);
                        $updatedCount++;
                        $this->info("  - Invoice updated with fallback data!");
                    }
                }
                
            } catch (Exception $e) {
                $this->error("  - Error processing invoice: " . $e->getMessage());
            }
            
            $this->newLine();
        }

        $this->info('=== Reprocessing Complete ===');
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
}
