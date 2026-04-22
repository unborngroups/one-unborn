<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vendor;

echo "=== Vendor Database Check ===" . PHP_EOL;

$vendorCount = Vendor::count();
echo "Total vendors in database: {$vendorCount}" . PHP_EOL . PHP_EOL;

if ($vendorCount > 0) {
    echo "Sample vendors:" . PHP_EOL;
    Vendor::take(5)->get(['vendor_name', 'gstin'])->each(function($vendor) {
        echo "- {$vendor->vendor_name} (GSTIN: {$vendor->gstin})" . PHP_EOL;
    });
} else {
    echo "No vendors found in database!" . PHP_EOL;
}

echo PHP_EOL . "=== GSTIN-based Invoice Check ===" . PHP_EOL;

use App\Models\PurchaseInvoice;

$invoicesWithGstin = PurchaseInvoice::whereNotNull('gstin')->orWhereNotNull('vendor_gstin')->limit(5)->get(['invoice_no', 'gstin', 'vendor_gstin', 'vendor_name']);

echo "Invoices with GSTIN:" . PHP_EOL;
foreach ($invoicesWithGstin as $invoice) {
    $gstin = $invoice->gstin ?? $invoice->vendor_gstin ?? 'N/A';
    echo "- {$invoice->invoice_no}: GSTIN {$gstin}, Vendor: {$invoice->vendor_name}" . PHP_EOL;
}
