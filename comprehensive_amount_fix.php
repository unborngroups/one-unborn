<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Comprehensive Amount Extraction Fix ===" . PHP_EOL;

// Test with actual invoice
$inv = App\Models\PurchaseInvoice::find(1);
if ($inv && $inv->po_invoice_file) {
    $pdfFile = public_path('images/poinvoice_files/' . $inv->po_invoice_file);
    echo "PDF File: " . $pdfFile . PHP_EOL;
    echo "Current Extracted Total: " . $inv->grand_total . PHP_EOL;
    
    if (file_exists($pdfFile)) {
        // Use the actual OCR service
        try {
            $ocrService = new \App\Services\OCRService();
            $result = $ocrService->extractInvoiceData($pdfFile);
            
            echo PHP_EOL . "=== OCR Results ===" . PHP_EOL;
            echo "Success: " . ($result['success'] ? 'Yes' : 'No') . PHP_EOL;
            
            if ($result['success'] && isset($result['data']['raw_text'])) {
                $rawText = $result['data']['raw_text'];
                echo "Raw OCR Text (first 1500 chars):" . PHP_EOL;
                echo substr($rawText, 0, 1500) . PHP_EOL;
                
                // Test original extractTotal function
                echo PHP_EOL . "=== Testing Original Function ===" . PHP_EOL;
                $original = extractTotalOriginal($rawText);
                echo "Original Function Extracts: " . $original . PHP_EOL;
                
                // Test corrected extractTotal function
                echo PHP_EOL . "=== Testing Corrected Function ===" . PHP_EOL;
                $corrected = extractTotalCorrected($rawText);
                echo "Corrected Function Extracts: " . $corrected . PHP_EOL;
                
                // Test all amounts found
                echo PHP_EOL . "=== All Amounts Found ===" . PHP_EOL;
                $allAmounts = findAllAmounts($rawText);
                foreach ($allAmounts as $amount) {
                    echo "Found: " . $amount . PHP_EOL;
                }
                
                // Test with specific patterns
                echo PHP_EOL . "=== Testing Specific Patterns ===" . PHP_EOL;
                testSpecificPatterns($rawText);
                
            } else {
                echo "Error: " . ($result['error'] ?? 'Unknown error') . PHP_EOL;
                if (isset($result['message'])) {
                    echo "Message: " . $result['message'] . PHP_EOL;
                }
            }
            
        } catch (Exception $e) {
            echo "OCR Service Error: " . $e->getMessage() . PHP_EOL;
        }
    } else {
        echo "PDF file not found" . PHP_EOL;
    }
} else {
    echo "No invoice found" . PHP_EOL;
}

// Original extractTotal function
function extractTotalOriginal(string $text): float
{
    $strongPatterns = [
        '/(?:grand\s*total|invoice\s*total|net\s*payable|amount\s*payable|total\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|_rupee|_rupee_|\u20B9)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        '/(?:balance\s*due|amount\s*due)\s*[:\-]?\s*(?:inr|rs\.?|_rupee|_rupee_|\u20B9)?\s*([\d,]+(?:\.\d{1,2})?)/i',
    ];

    foreach ($strongPatterns as $pattern) {
        if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
            $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
            return max($values);
        }
    }

    if (preg_match_all('/\btotal\b\s*[:\-]?\s*(?:inr|rs\.?|_rupee|_rupee_|\u20B9)?\s*([\d,]+(?:\.\d{1,2})?)/i', $text, $matches) && !empty($matches[1])) {
        $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
        return max($values);
    }

    return 0.0;
}

// Corrected extractTotal function
function extractTotalCorrected(string $text): float
{
    // Enhanced patterns with proper currency symbol handling
    $strongPatterns = [
        // Strong patterns with explicit total keywords
        '/(?:grand\s*total|invoice\s*total|net\s*payable|amount\s*payable|total\s*amount|total\s*due|balance\s*due|payable\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        '/(?:balance\s*due|amount\s*due)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
    ];

    foreach ($strongPatterns as $pattern) {
        if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
            $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
            // Filter out very small amounts (likely not totals) and return the largest
            $validAmounts = array_filter($values, fn($v) => $v > 10);
            if (!empty($validAmounts)) {
                return max($validAmounts);
            }
        }
    }

    // Enhanced fallback patterns
    $fallbackPatterns = [
        '/\btotal\b\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        '/\b(?:total|amount|payable)\b.*?(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/im',
        '/(?:total\s*amount|invoice\s*amount|net\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
    ];

    foreach ($fallbackPatterns as $pattern) {
        if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
            $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
            $validAmounts = array_filter($values, fn($v) => $v > 10);
            if (!empty($validAmounts)) {
                return max($validAmounts);
            }
        }
    }

    return 0.0;
}

// Find all amounts in text
function findAllAmounts($text): array
{
    $amounts = [];
    
    // Find all numbers with optional commas and decimals
    if (preg_match_all('/\b([\d,]+(?:\.\d{1,2})?)\b/', $text, $matches)) {
        foreach ($matches[1] as $match) {
            $amount = (float) str_replace(',', '', $match);
            if ($amount > 0) {
                $amounts[] = $amount;
            }
        }
    }
    
    sort($amounts);
    return array_unique($amounts);
}

// Test specific patterns
function testSpecificPatterns($text): array
{
    $patterns = [
        'Total amount' => '/total\s*amount\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        'Grand total' => '/grand\s*total\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        'Invoice total' => '/invoice\s*total\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        'Net payable' => '/net\s*payable\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        'Amount payable' => '/amount\s*payable\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        'Balance due' => '/balance\s*due\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        'Total' => '/\btotal\b\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i',
    ];
    
    $results = [];
    foreach ($patterns as $name => $pattern) {
        if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
            $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
            $results[$name] = $values;
        }
    }
    
    foreach ($results as $name => $values) {
        echo $name . ": " . implode(', ', $values) . PHP_EOL;
    }
    
    return $results;
}
?>
