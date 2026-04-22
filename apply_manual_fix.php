<?php
// Manual fix application for extractTotal function
$filePath = 'app/Console/Commands/FetchGmailInvoicesCommand.php';

echo "Applying manual fix to extractTotal function..." . PHP_EOL;

// Read the file
$content = file_get_contents($filePath);
if ($content === false) {
    echo "Failed to read file" . PHP_EOL;
    exit(1);
}

// The exact old function to replace
$oldFunction = '    private function extractTotal(string $text): float
    {
        $strongPatterns = [
            \'/\*(?:grand\s*total|invoice\s*total|net\s*payable|amount\s*payable|total\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|\u20B9)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
            \'/\*(?:balance\s*due|amount\s*due)\s*[:\-]?\s*(?:inr|rs\.?|\u20B9)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
        ];

        foreach ($strongPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
                $values = array_map(fn ($v) => (float) str_replace(\',\', \'\', $v), $matches[1]);
                return max($values);
            }
        }

        if (preg_match_all(\'/\btotal\b\s*[:\-]?\s*(?:inr|rs\.?|\u20B9)?\s*([\d,]+(?:\.\d{1,2})?)/i\', $text, $matches) && !empty($matches[1])) {
            $values = array_map(fn ($v) => (float) str_replace(\',\', \'\', $v), $matches[1]);
            return max($values);
        }

        return 0.0;
    }';

// The new corrected function
$newFunction = '    private function extractTotal(string $text): float
    {
        // Enhanced patterns for better amount extraction
        $strongPatterns = [
            // Strong patterns with explicit total keywords
            \'/\*(?:grand\s*total|invoice\s*total|net\s*payable|amount\s*payable|total\s*amount|total\s*due|balance\s*due|payable\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
            \'/\*(?:balance\s*due|amount\s*due)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
        ];

        foreach ($strongPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
                $values = array_map(fn ($v) => (float) str_replace(\',\', \'\', $v), $matches[1]);
                // Filter out very small amounts (likely not totals) and return the largest
                $validAmounts = array_filter($values, fn($v) => $v > 10);
                if (!empty($validAmounts)) {
                    return max($validAmounts);
                }
            }
        }

        // Enhanced fallback patterns
        $fallbackPatterns = [
            \'/\btotal\b\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
            \'/\b(?:total|amount|payable)\b.*?(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/im\',
            \'/\*(?:total\s*amount|invoice\s*amount|net\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\',
        ];

        foreach ($fallbackPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
                $values = array_map(fn ($v) => (float) str_replace(\',\', \'\', $v), $matches[1]);
                $validAmounts = array_filter($values, fn($v) => $v > 10);
                if (!empty($validAmounts)) {
                    return max($validAmounts);
                }
            }
        }

        // Final fallback: Look for any amount with currency symbol in the last 20% of the text
        $textLines = explode("\n", $text);
        $lastLines = array_slice($textLines, max(0, count($textLines) - round(count($textLines) * 0.2)));
        $lastText = implode("\n", $lastLines);
        
        if (preg_match_all(\'/(?:inr|rs\.?|Rs|INR)?\s*([\d,]+(?:\.\d{1,2})?)/i\', $lastText, $matches) && !empty($matches[1])) {
            $values = array_map(fn ($v) => (float) str_replace(\',\', \'\', $v), $matches[1]);
            $validAmounts = array_filter($values, fn($v) => $v > 10);
            if (!empty($validAmounts)) {
                return max($validAmounts);
            }
        }

        return 0.0;
    }';

// Find the exact function in the file
$startPos = strpos($content, '    private function extractTotal(string $text): float');
if ($startPos !== false) {
    // Find the end of the function
    $endPos = strpos($content, "\n    }", $startPos);
    if ($endPos !== false) {
        $endPos += 6; // Include the closing brace and newline
        
        // Extract the old function
        $oldFunctionExtract = substr($content, $startPos, $endPos - $startPos);
        
        // Replace with new function
        $newContent = substr_replace($content, $newFunction, $startPos, $endPos - $startPos);
        
        // Write back to file
        if (file_put_contents($filePath, $newContent) !== false) {
            echo "Successfully applied the extractTotal function fix!" . PHP_EOL;
            
            // Verify the change
            $verifyContent = file_get_contents($filePath);
            $verifyStart = strpos($verifyContent, '// Enhanced patterns for better amount extraction');
            if ($verifyStart !== false) {
                echo "Fix verified successfully!" . PHP_EOL;
            } else {
                echo "Fix may not have been applied correctly" . PHP_EOL;
            }
        } else {
            echo "Failed to write the updated file" . PHP_EOL;
        }
    } else {
        echo "Could not find end of function" . PHP_EOL;
    }
} else {
    echo "Could not find extractTotal function" . PHP_EOL;
}
?>
