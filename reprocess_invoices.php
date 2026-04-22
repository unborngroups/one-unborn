<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Reprocessing Existing Invoices with Corrected Function ===" . PHP_EOL;

// Get invoices that need reprocessing
$invoices = App\Models\PurchaseInvoice::whereNotNull('po_invoice_file')->take(3)->get();

foreach ($invoices as $inv) {
    echo PHP_EOL . "Processing Invoice ID: " . $inv->id . PHP_EOL;
    echo "Current Grand Total: " . $inv->grand_total . PHP_EOL;
    
    $pdfFile = public_path('images/poinvoice_files/' . $inv->po_invoice_file);
    if (!file_exists($pdfFile)) {
        echo "PDF file not found, skipping..." . PHP_EOL;
        continue;
    }
    
    try {
        // Use the actual command class to reprocess
        $command = new \App\Console\Commands\FetchGmailInvoicesCommand();
        
        // Use reflection to access the private parseInvoice method
        $reflection = new ReflectionClass($command);
        $parseMethod = $reflection->getMethod('parseInvoice');
        $parseMethod->setAccessible(true);
        
        $result = $parseMethod->invoke($command, $pdfFile);
        
        if (isset($result['total']) && $result['total'] > 0) {
            echo "New Extracted Total: " . $result['total'] . PHP_EOL;
            
            // Update the invoice with the corrected amount
            $inv->update([
                'grand_total' => $result['total'],
                'total_amount' => $result['total'],
                'raw_json' => array_merge($inv->raw_json ?? [], $result)
            ]);
            
            echo "Invoice updated successfully!" . PHP_EOL;
            
            if ($result['total'] != $inv->grand_total) {
                echo "AMOUNT CHANGED: " . $inv->grand_total . " -> " . $result['total'] . PHP_EOL;
            } else {
                echo "Amount remains the same" . PHP_EOL;
            }
        } else {
            echo "Failed to extract total from PDF" . PHP_EOL;
        }
        
    } catch (Exception $e) {
        echo "Error processing invoice: " . $e->getMessage() . PHP_EOL;
    }
    
    echo str_repeat("-", 50) . PHP_EOL;
}

echo PHP_EOL . "=== Verification ===" . PHP_EOL;

// Verify the updates
$updatedInvoices = App\Models\PurchaseInvoice::take(3)->get(['id', 'amount', 'total_amount', 'grand_total']);

foreach ($updatedInvoices as $inv) {
    echo "Invoice ID: " . $inv->id . PHP_EOL;
    echo "Updated Grand Total: " . $inv->grand_total . PHP_EOL;
    echo PHP_EOL;
}

echo "Reprocessing completed!" . PHP_EOL;
?>
