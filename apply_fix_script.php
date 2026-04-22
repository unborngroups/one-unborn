<?php
// Script to apply the corrected extractTotal function
$filePath = 'app/Console/Commands/FetchGmailInvoicesCommand.php';
$backupPath = 'app/Console/Commands/FetchGmailInvoicesCommand.php.backup';

echo "Applying extractTotal function fix..." . PHP_EOL;

// Create backup
if (file_exists($filePath)) {
    copy($filePath, $backupPath);
    echo "Backup created at: $backupPath" . PHP_EOL;
} else {
    echo "Source file not found: $filePath" . PHP_EOL;
    exit(1);
}

// Read the current file
$content = file_get_contents($filePath);
if ($content === false) {
    echo "Failed to read file: $filePath" . PHP_EOL;
    exit(1);
}

// Define the old function (what we're replacing)
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

// Define the new function (the corrected version)
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

// Try to find and replace the function
$pattern = '/private function extractTotal\(string \$text\): float\s*\{[^}]*\n\s*\}/';
if (preg_match($pattern, $content, $matches)) {
    echo "Found extractTotal function to replace" . PHP_EOL;
    $newContent = preg_replace($pattern, $newFunction, $content);
    
    if ($newContent !== null && $newContent !== $content) {
        // Write the updated content
        if (file_put_contents($filePath, $newContent) !== false) {
            echo "Successfully applied the extractTotal function fix!" . PHP_EOL;
            echo "Backup available at: $backupPath" . PHP_EOL;
        } else {
            echo "Failed to write updated content to file" . PHP_EOL;
            exit(1);
        }
    } else {
        echo "Failed to replace the function" . PHP_EOL;
        exit(1);
    }
} else {
    echo "Could not find extractTotal function to replace" . PHP_EOL;
    echo "Please check the file structure and apply the fix manually" . PHP_EOL;
    exit(1);
}

echo "Fix application completed!" . PHP_EOL;
?>
