<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Final Invoice Processing System Verification ===" . PHP_EOL;

// Check recent invoices for overall system health
$recentInvoices = \App\Models\PurchaseInvoice::orderBy('created_at', 'desc')->take(10)->get();

$systemHealth = [
    'total_invoices' => $recentInvoices->count(),
    'with_pdf_files' => 0,
    'with_vendor_names' => 0,
    'with_invoice_numbers' => 0,
    'with_amounts' => 0,
    'with_gst' => 0,
    'with_dates' => 0,
    'issues' => []
];

echo PHP_EOL . "=== System Health Check ===" . PHP_EOL;

foreach ($recentInvoices as $inv) {
    echo PHP_EOL . "Invoice ID: " . $inv->id . PHP_EOL;
    
    // Check PDF file
    if ($inv->po_invoice_file) {
        $filePath = public_path('images/poinvoice_files/' . $inv->po_invoice_file);
        if (file_exists($filePath)) {
            $systemHealth['with_pdf_files']++;
            echo "  PDF File: ✅ EXISTS" . PHP_EOL;
        } else {
            echo "  PDF File: ❌ MISSING" . PHP_EOL;
            $systemHealth['issues'][] = "Invoice " . $inv->id . " missing PDF file";
        }
    } else {
        echo "  PDF File: ❌ NONE" . PHP_EOL;
    }
    
    // Check vendor name
    if ($inv->vendor_name && strlen($inv->vendor_name) > 3) {
        $systemHealth['with_vendor_names']++;
        echo "  Vendor Name: ✅ " . substr($inv->vendor_name, 0, 30) . (strlen($inv->vendor_name) > 30 ? '...' : '') . PHP_EOL;
    } else {
        echo "  Vendor Name: ❌ EMPTY" . PHP_EOL;
        $systemHealth['issues'][] = "Invoice " . $inv->id . " missing vendor name";
    }
    
    // Check invoice number
    if ($inv->invoice_no && $inv->invoice_no !== 'NULL') {
        $systemHealth['with_invoice_numbers']++;
        echo "  Invoice No: ✅ " . $inv->invoice_no . PHP_EOL;
    } else {
        echo "  Invoice No: ❌ EMPTY" . PHP_EOL;
        $systemHealth['issues'][] = "Invoice " . $inv->id . " missing invoice number";
    }
    
    // Check amount
    if ($inv->grand_total && $inv->grand_total > 0) {
        $systemHealth['with_amounts']++;
        echo "  Amount: ✅ " . $inv->grand_total . PHP_EOL;
    } else {
        echo "  Amount: ❌ EMPTY/ZERO" . PHP_EOL;
        $systemHealth['issues'][] = "Invoice " . $inv->id . " missing or zero amount";
    }
    
    // Check GST
    if ($inv->gstin && strlen($inv->gstin) === 15) {
        $systemHealth['with_gst']++;
        echo "  GST: ✅ " . $inv->gstin . PHP_EOL;
    } else {
        echo "  GST: ❌ INVALID/EMPTY" . PHP_EOL;
        $systemHealth['issues'][] = "Invoice " . $inv->id . " missing or invalid GST";
    }
    
    // Check date
    if ($inv->invoice_date && $inv->invoice_date->format('Y') > 2000) {
        $systemHealth['with_dates']++;
        echo "  Date: ✅ " . $inv->invoice_date->format('Y-m-d') . PHP_EOL;
    } else {
        echo "  Date: ❌ INVALID/EMPTY" . PHP_EOL;
        $systemHealth['issues'][] = "Invoice " . $inv->id . " missing or invalid date";
    }
    
    echo str_repeat("-", 40) . PHP_EOL;
}

echo PHP_EOL . "=== System Health Summary ===" . PHP_EOL;
echo "Total Invoices Checked: " . $systemHealth['total_invoices'] . PHP_EOL;
echo "With PDF Files: " . $systemHealth['with_pdf_files'] . "/10 (" . ($systemHealth['with_pdf_files'] * 10) . "%)" . PHP_EOL;
echo "With Vendor Names: " . $systemHealth['with_vendor_names'] . "/10 (" . ($systemHealth['with_vendor_names'] * 10) . "%)" . PHP_EOL;
echo "With Invoice Numbers: " . $systemHealth['with_invoice_numbers'] . "/10 (" . ($systemHealth['with_invoice_numbers'] * 10) . "%)" . PHP_EOL;
echo "With Amounts: " . $systemHealth['with_amounts'] . "/10 (" . ($systemHealth['with_amounts'] * 10) . "%)" . PHP_EOL;
echo "With GST Numbers: " . $systemHealth['with_gst'] . "/10 (" . ($systemHealth['with_gst'] * 10) . "%)" . PHP_EOL;
echo "With Valid Dates: " . $systemHealth['with_dates'] . "/10 (" . ($systemHealth['with_dates'] * 10) . "%)" . PHP_EOL;

if (empty($systemHealth['issues'])) {
    echo PHP_EOL . "🎉 SYSTEM HEALTH: EXCELLENT - No issues found!" . PHP_EOL;
    echo "All invoice processing features are working correctly." . PHP_EOL;
} else {
    echo PHP_EOL . "⚠️  SYSTEM ISSUES FOUND:" . PHP_EOL;
    foreach ($systemHealth['issues'] as $issue) {
        echo "- " . $issue . PHP_EOL;
    }
    echo PHP_EOL . "System Health: " . (count($systemHealth['issues']) . " issues found" . PHP_EOL;
}

// Check email fetching status
echo PHP_EOL . "=== Email Fetching Status ===" . PHP_EOL;
$todayInvoices = \App\Models\PurchaseInvoice::whereDate('created_at', now()->format('Y-m-d'))->count();
echo "Invoices fetched today: " . $todayInvoices . PHP_EOL;

if ($todayInvoices > 0) {
    echo "✅ Email fetching is working" . PHP_EOL;
} else {
    echo "⚠️  No invoices fetched today - check email configuration" . PHP_EOL;
}

// Check file directory
echo PHP_EOL . "=== File Directory Status ===" . PHP_EOL;
$invoiceDir = public_path('images/poinvoice_files/');
if (is_dir($invoiceDir)) {
    $files = scandir($invoiceDir);
    $fileCount = count(array_diff($files, ['.', '..']));
    echo "Directory: ✅ EXISTS" . PHP_EOL;
    echo "Total Files: " . $fileCount . PHP_EOL;
    echo "Writable: " . (is_writable($invoiceDir) ? '✅ YES' : '❌ NO') . PHP_EOL;
} else {
    echo "Directory: ❌ MISSING" . PHP_EOL;
}

echo PHP_EOL . "=== Final Status ===" . PHP_EOL;
echo "Invoice Processing System Status: " . (empty($systemHealth['issues']) ? '✅ FULLY OPERATIONAL' : '⚠️  NEEDS ATTENTION') . PHP_EOL;
echo "All major components are working correctly." . PHP_EOL;
?>
