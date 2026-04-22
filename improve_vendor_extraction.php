<?php
// Improve the vendor name extraction to be more precise

$filePath = 'app/Console/Commands/FetchGmailInvoicesCommand.php';

echo "Improving vendor name extraction from invoice PDFs..." . PHP_EOL;

// Read the file
$content = file_get_contents($filePath);

// Define the improved extractVendorName function
$newFunction = '    private function extractVendorName(string $text): ?string
    {
        $linePatterns = [
            // More precise patterns for vendor name extraction
            \'/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,80})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Ship\s+To|\s+Address|\s+Email|\s+Phone|\s+Mobile)/mi\',
            \'/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,80})(?=\R{2,}|\R\s*[A-Z])/mi\',
        ];

        foreach ($linePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $vendorName = trim($matches[1]);
                
                // Clean up the vendor name
                $vendorName = preg_replace(\'/["""]/\', \'\', $vendorName); // Remove quotes
                $vendorName = preg_replace(\'/\s{2,}/\', \' \', $vendorName); // Replace multiple spaces with single space
                $vendorName = preg_replace(\'/\\r|\\n/\', \'\', $vendorName); // Remove newlines
                $vendorName = trim($vendorName);
                
                // Additional cleaning for common patterns
                $vendorName = preg_replace(\'/\\s+(Pvt\\s+Ltd|Private\\s+Limited|LLP|Ltd|Limited)\\s*$/i\', \' $1\', $vendorName);
                
                // Return only if it looks like a valid company name
                if (strlen($vendorName) >= 5 && strlen($vendorName) <= 100 && preg_match(\'/[A-Za-z]/\', $vendorName)) {
                    return $vendorName;
                }
            }
        }

        // Fallback patterns for different invoice formats
        $fallbackPatterns = [
            \'/\bFrom\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,80})(?=\R|\s+GSTIN|\s+To|\s+Date)/mi\',
            \'/\bSupplier\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,80})(?=\R|\s+GSTIN|\s+Address)/mi\',
        ];

        foreach ($fallbackPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $vendorName = trim($matches[1]);
                $vendorName = preg_replace(\'/["""]/\', \'\', $vendorName);
                $vendorName = preg_replace(\'/\s{2,}/\', \' \', $vendorName);
                $vendorName = preg_replace(\'/\\r|\\n/\', \'\', $vendorName);
                $vendorName = trim($vendorName);
                
                if (strlen($vendorName) >= 5 && strlen($vendorName) <= 100 && preg_match(\'/[A-Za-z]/\', $vendorName)) {
                    return $vendorName;
                }
            }
        }

        return null;
    }';

// Find and replace the extractVendorName function
$startPos = strpos($content, '    private function extractVendorName(string $text): ?string');
if ($startPos !== false) {
    $endPos = strpos($content, "\n    }", $startPos);
    if ($endPos !== false) {
        $endPos += 6;
        
        $newContent = substr_replace($content, $newFunction, $startPos, $endPos - $startPos);
        
        if (file_put_contents($filePath, $newContent) !== false) {
            echo "Successfully improved extractVendorName function!" . PHP_EOL;
        } else {
            echo "Failed to write updated file" . PHP_EOL;
        }
    } else {
        echo "Could not find end of function" . PHP_EOL;
    }
} else {
    echo "Could not find extractVendorName function" . PHP_EOL;
}

echo PHP_EOL . "=== Testing Improved Function ===" . PHP_EOL;

// Test the improved function
$testText = "POWERED BY
Sub Total       5,300.00
CGST9 (9%)      477.00
SGST9 (9%)      477.00
Total           6,254.00
Balance Due     6,254.00
Invoice Date : 06/04/2026
Terms : Due on Receipt
Due Date : 06/04/2026
SPEEDOBITS INTERNET PRIVATE LIMITED
Shop No : 204, Jasal Complex, Opp. Strerling Hospital, 150ft
Ringroad
Rajkot Gujarat 360007
India
GSTIN 24ABDCS7989K1ZK
INVOICE
Invoice# SIPL/26-27/G29
Balance Due
6,254.00
Bill To
INFRASPOT SOLUTIONS PRIVATE LIMITED
\"\"Sundaram Finance Limited\"\"  4rth Floor, Office No. 402 & 403, Akruti
Bizhub,
Nr. Raiya Tele. Exchange, 150FT. Ring Road\"
Rajkot
 Gujarat
India";

$tester = new class {
    public function extractVendorName(string $text): ?string
    {
        $linePatterns = [
            // More precise patterns for vendor name extraction
            \'/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,80})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Ship\s+To|\s+Address|\s+Email|\s+Phone|\s+Mobile)/mi\',
            \'/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,80})(?=\R{2,}|\R\s*[A-Z])/mi\',
        ];

        foreach ($linePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $vendorName = trim($matches[1]);
                
                // Clean up the vendor name
                $vendorName = preg_replace(\'/["""]/\', \'\', $vendorName); // Remove quotes
                $vendorName = preg_replace(\'/\s{2,}/\', \' \', $vendorName); // Replace multiple spaces with single space
                $vendorName = preg_replace(\'/\\r|\\n/\', \'\', $vendorName); // Remove newlines
                $vendorName = trim($vendorName);
                
                // Additional cleaning for common patterns
                $vendorName = preg_replace(\'/\\s+(Pvt\\s+Ltd|Private\\s+Limited|LLP|Ltd|Limited)\\s*$/i\', \' $1\', $vendorName);
                
                // Return only if it looks like a valid company name
                if (strlen($vendorName) >= 5 && strlen($vendorName) <= 100 && preg_match(\'/[A-Za-z]/\', $vendorName)) {
                    return $vendorName;
                }
            }
        }

        return null;
    }
};

echo "Test Text:" . PHP_EOL;
echo $testText . PHP_EOL;
echo "Improved Function Extracts: " . ($tester->extractVendorName($testText) ?? 'NULL') . PHP_EOL;

echo PHP_EOL . "Vendor extraction improvement completed!" . PHP_EOL;
?>
