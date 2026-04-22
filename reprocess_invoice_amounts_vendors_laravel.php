<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\OCRService;
use App\Services\InvoiceParserService;
use App\Models\PurchaseInvoice;

/**
 * Reprocess existing invoices with improved amount and vendor extraction logic
 */

echo "=== Invoice Reprocessing Script ===" . PHP_EOL;
echo "This will reprocess existing invoices with improved extraction logic." . PHP_EOL . PHP_EOL;

// Initialize services
$ocrService = new OCRService();
$invoiceParserService = new InvoiceParserService();

// Get invoices that need reprocessing (low accuracy or needs review)
$invoicesToReprocess = PurchaseInvoice::query()
    ->where(function($query) {
        $query->where('confidence_score', '<', 80)
              ->orWhere('confidence_score', null)
              ->orWhere('status', 'needs_review')
              ->orWhere('status', 'draft');
    })
    ->whereNotNull('po_invoice_file')
    ->limit(20) // Process in smaller batches
    ->get();

echo "Found " . count($invoicesToReprocess) . " invoices to reprocess." . PHP_EOL . PHP_EOL;

$processedCount = 0;
$updatedCount = 0;

foreach ($invoicesToReprocess as $invoice) {
    $processedCount++;
    echo "Processing invoice #{$processedCount}: {$invoice->invoice_no}" . PHP_EOL;
    
    // Get PDF file path
    $pdfPath = null;
    if (!empty($invoice->po_invoice_file)) {
        $candidatePath = public_path('images/poinvoice_files/' . $invoice->po_invoice_file);
        if (file_exists($candidatePath)) {
            $pdfPath = $candidatePath;
        }
    }
    
    if (!$pdfPath) {
        echo "  - PDF file not found, skipping..." . PHP_EOL;
        continue;
    }
    
    try {
        // Extract data using improved OCR service
        $ocrResult = $ocrService->extractInvoiceData($pdfPath);
        
        if ($ocrResult['success']) {
            $extractedData = $ocrResult['data'];
            
            echo "  - OCR extraction successful" . PHP_EOL;
            echo "  - Vendor: " . ($extractedData['vendor_name'] ?? 'N/A') . PHP_EOL;
            echo "  - Total: " . ($extractedData['total'] ?? 'N/A') . PHP_EOL;
            echo "  - Confidence: " . ($extractedData['confidence'] ?? 'N/A') . "%" . PHP_EOL;
            
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
                echo "  - Invoice updated successfully!" . PHP_EOL;
            } else {
                echo "  - No improvements found, keeping original data." . PHP_EOL;
            }
            
        } else {
            echo "  - OCR extraction failed: " . ($ocrResult['error'] ?? 'Unknown error') . PHP_EOL;
            
            // Try fallback parser
            $fallbackResult = $invoiceParserService->parse($pdfPath);
            if ($fallbackResult['success']) {
                $extractedData = $fallbackResult['data'];
                echo "  - Fallback parser successful" . PHP_EOL;
                
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
                echo "  - Invoice updated with fallback data!" . PHP_EOL;
            }
        }
        
    } catch (Exception $e) {
        echo "  - Error processing invoice: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL;
}

echo "=== Reprocessing Complete ===" . PHP_EOL;
echo "Total invoices processed: {$processedCount}" . PHP_EOL;
echo "Invoices updated: {$updatedCount}" . PHP_EOL;
echo "Remaining invoices to process: " . (PurchaseInvoice::where('confidence_score', '<', 80)->orWhere('confidence_score', null)->count()) . PHP_EOL;

if ($updatedCount > 0) {
    echo PHP_EOL . "Please refresh the Auto Invoice Processing page to see the improvements." . PHP_EOL;
}
