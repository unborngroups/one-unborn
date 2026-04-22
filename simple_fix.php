<?php
// Simple fix for extractTotal function to prioritize "Total" with currency symbol

$filePath = 'app/Console/Commands/FetchGmailInvoicesCommand.php';

echo "Applying simple fix to extractTotal function..." . PHP_EOL;

// Read the file
$content = file_get_contents($filePath);

// Find and replace the strongPatterns array to prioritize "Total" with currency symbol
$oldPatterns = '        $strongPatterns = [
            // Strong patterns with explicit total keywords
            \'/\*(?:grand\s*total|invoice\s*total|net\s*payable|amount\s*payable|total\s*amount|total\s*due|balance\s*due|payable\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
            \'/\*(?:balance\s*due|amount\s*due)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
        ];';

$newPatterns = '        $strongPatterns = [
            // Prioritize "Total" with currency symbol (most accurate)
            \'/Total\s*[\u20B9Rs\.INR]*\s*([\d,]+(?:\.\d{1,2})?)/i\',
            \'/Balance\s*Due\s*[\u20B9Rs\.INR]*\s*([\d,]+(?:\.\d{1,2})?)/i\',
            // Strong patterns with explicit total keywords
            \'/\*(?:grand\s*total|invoice\s*total|net\s*payable|amount\s*payable|total\s*amount|total\s*due|balance\s*due|payable\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
            \'/\*(?:balance\s*due|amount\s*due)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
        ];';

// Replace the patterns
$newContent = str_replace($oldPatterns, $newPatterns, $content);

if ($newContent !== $content) {
    if (file_put_contents($filePath, $newContent) !== false) {
        echo "Successfully updated extractTotal function!" . PHP_EOL;
        
        // Test the updated function
        echo PHP_EOL . "=== Testing Updated Function ===" . PHP_EOL;
        
        // Create a test instance
        $command = new class {
            public function extractTotal(string $text): float
            {
                // Enhanced patterns for better amount extraction
                $strongPatterns = [
                    // Prioritize "Total" with currency symbol (most accurate)
                    '/Total\s*[\u20B9Rs\.INR]*\s*([\d,]+(?:\.\d{1,2})?)/i',
                    '/Balance\s*Due\s*[\u20B9Rs\.INR]*\s*([\d,]+(?:\.\d{1,2})?)/i',
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
        };
        
        // Test with the actual invoice text
        $testText = "POWERED BY
Sub Total       5,300.00
CGST9 (9%)      477.00
SGST9 (9%)      477.00
Total           6,254.00
Balance Due     6,254.00";
        
        echo "Test Text:" . PHP_EOL;
        echo $testText . PHP_EOL;
        echo "Expected: 6254" . PHP_EOL;
        echo "Extracted: " . $command->extractTotal($testText) . PHP_EOL;
        
    } else {
        echo "Failed to write updated file" . PHP_EOL;
    }
} else {
    echo "No changes made - pattern not found" . PHP_EOL;
}

echo PHP_EOL . "Fix completed!" . PHP_EOL;
?>
