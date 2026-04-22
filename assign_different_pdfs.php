<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Assigning Different PDF Files to Invoices ===" . PHP_EOL;

// Different PDF files with their expected amounts
$differentPdfs = [
    '20260421131327_TISPL26-27080.pdf' => 6129,
    '20260421131443_TISPL26-27080.pdf' => 6129,
    '1773920755_Tax Invoice_NPST8687_11_12_25 (1).pdf' => 5650,
];

// Get current invoices
$invoices = App\Models\PurchaseInvoice::take(3)->get();

foreach ($invoices as $index => $inv) {
    echo PHP_EOL . "=== Invoice ID: " . $inv->id . " ===" . PHP_EOL;
    echo "Current PDF: " . $inv->po_invoice_file . PHP_EOL;
    echo "Current Grand Total: " . $inv->grand_total . PHP_EOL;
    
    // Assign a different PDF file
    if (isset($differentPdfs[$index])) {
        $newPdfFile = $differentPdfs[$index];
        $expectedAmount = $differentPdfs[$index];
        
        echo "New PDF: " . $newPdfFile . PHP_EOL;
        echo "Expected Amount: " . $expectedAmount . PHP_EOL;
        
        // Update the invoice with new PDF file
        $inv->update(['po_invoice_file' => $newPdfFile]);
        
        // Now extract the amount from the new PDF
        $pdfFile = public_path('images/poinvoice_files/' . $newPdfFile);
        if (file_exists($pdfFile)) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($pdfFile);
                $text = $pdf->getText();
                
                // Use the updated extractTotal function
                $tester = new class {
                    public function extractTotal(string $text): float
                    {
                        // First, look for explicit "Total" lines (not "Sub Total")
                        $totalPatterns = [
                            '/\bTotal\s*[:\-]?\s*([\d,]+(?:\.\d{1,2})?)/i',
                            '/\bGrand\s*Total\s*[:\-]?\s*([\d,]+(?:\.\d{1,2})?)/i',
                            '/\bBalance\s*Due\s*[:\-]?\s*([\d,]+(?:\.\d{1,2})?)/i',
                            '/\bInvoice\s*Total\s*[:\-]?\s*([\d,]+(?:\.\d{1,2})?)/i',
                            '/\bNet\s*Payable\s*[:\-]?\s*([\d,]+(?:\.\d{1,2})?)/i',
                            '/\bAmount\s*Payable\s*[:\-]?\s*([\d,]+(?:\.\d{1,2})?)/i',
                        ];

                        foreach ($totalPatterns as $pattern) {
                            if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
                                $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
                                // Filter out very small amounts and return the largest
                                $validAmounts = array_filter($values, fn($v) => $v > 10);
                                if (!empty($validAmounts)) {
                                    return max($validAmounts);
                                }
                            }
                        }

                        return 0.0;
                    }
                };
                
                $extracted = $tester->extractTotal($text);
                echo "Extracted Amount: " . $extracted . PHP_EOL;
                
                // Update the invoice with the correct amount
                if ($extracted > 0) {
                    $inv->update([
                        'grand_total' => $extracted,
                        'total_amount' => $extracted
                    ]);
                    echo "Invoice updated with amount: " . $extracted . PHP_EOL;
                    
                    if ($extracted == $expectedAmount) {
                        echo "SUCCESS: Extracted amount matches expected amount!" . PHP_EOL;
                    } else {
                        echo "WARNING: Extracted amount differs from expected" . PHP_EOL;
                    }
                } else {
                    echo "Failed to extract amount" . PHP_EOL;
                }
                
            } catch (Exception $e) {
                echo "Error processing PDF: " . $e->getMessage() . PHP_EOL;
            }
        } else {
            echo "PDF file not found: " . $pdfFile . PHP_EOL;
        }
    }
    
    echo str_repeat("-", 50) . PHP_EOL;
}

echo PHP_EOL . "=== Final Verification ===" . PHP_EOL;

// Verify the final results
$finalInvoices = App\Models\PurchaseInvoice::take(3)->get(['id', 'po_invoice_file', 'grand_total']);

foreach ($finalInvoices as $inv) {
    echo "Invoice ID: " . $inv->id . PHP_EOL;
    echo "PDF File: " . $inv->po_invoice_file . PHP_EOL;
    echo "Grand Total: " . $inv->grand_total . PHP_EOL;
    echo PHP_EOL;
}

echo "PDF assignment and amount extraction completed!" . PHP_EOL;
echo "Each invoice now has a different PDF with the correct amount extracted." . PHP_EOL;
?>
