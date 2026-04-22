<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;
use Smalot\PdfParser\Parser;

class CheckDateExtractionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-dates {--limit=20 : Number of invoices to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check date extraction accuracy from invoice PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Checking Date Extraction from Invoice PDFs ===');
        $this->newLine();

        // Get recent invoices to check
        $limit = $this->option('limit');
        $invoices = PurchaseInvoice::whereNotNull('po_invoice_file')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'invoice_no', 'invoice_date', 'vendor_name', 'total_amount']);

        $this->info("Checking " . count($invoices) . " recent invoices for date extraction accuracy:");
        $this->newLine();

        $issuesFound = 0;
        $checkedCount = 0;

        foreach ($invoices as $invoice) {
            $checkedCount++;
            $this->info("Checking invoice #{$checkedCount}: {$invoice->invoice_no}");
            $this->line("  Current Date: " . ($invoice->invoice_date ?? 'NULL'));
            $this->line("  Vendor: {$invoice->vendor_name}");
            $this->line("  Total: {$invoice->total_amount}");

            // Get PDF file path
            $pdfPath = null;
            if (!empty($invoice->po_invoice_file)) {
                $candidatePath = public_path('images/poinvoice_files/' . $invoice->po_invoice_file);
                if (file_exists($candidatePath)) {
                    $pdfPath = $candidatePath;
                }
            }

            if (!$pdfPath) {
                $this->warn("  - PDF file not found, skipping...");
                $this->newLine();
                continue;
            }

            try {
                // Extract text from PDF
                $parser = new Parser();
                $pdf = $parser->parseFile($pdfPath);
                $text = $pdf->getText();

                if (empty($text)) {
                    $this->warn("  - Could not extract text from PDF");
                    $this->newLine();
                    continue;
                }

                // Extract dates using enhanced patterns
                $extractedDates = $this->extractAllDates($text);
                
                $this->line("  Dates found in PDF: " . implode(', ', $extractedDates));
                
                // Check if current date matches any extracted date
                $dateMatch = false;
                if ($invoice->invoice_date) {
                    foreach ($extractedDates as $extractedDate) {
                        if ($extractedDate === $invoice->invoice_date) {
                            $dateMatch = true;
                            break;
                        }
                    }
                }

                if ($dateMatch) {
                    $this->info("  - Date extraction: CORRECT");
                } else {
                    $this->warn("  - Date extraction: ISSUE DETECTED");
                    $issuesFound++;
                    
                    if (!$invoice->invoice_date) {
                        $this->warn("    - No date stored in database");
                    } else {
                        $this->warn("    - Stored date '{$invoice->invoice_date}' not found in PDF");
                    }
                    
                    // Try to fix the date
                    if (!empty($extractedDates)) {
                        $bestDate = $this->selectBestDate($extractedDates);
                        $this->line("    - Suggested fix: {$bestDate}");
                        
                        // Ask if user wants to apply fix
                        if ($this->confirm("Apply this date fix?")) {
                            $invoice->update(['invoice_date' => $bestDate]);
                            $this->info("    - Date fixed successfully!");
                        }
                    }
                }

            } catch (Exception $e) {
                $this->error("  - Error processing PDF: " . $e->getMessage());
                $issuesFound++;
            }

            $this->newLine();
        }

        $this->info('=== Date Extraction Check Complete ===');
        $this->info("Invoices checked: {$checkedCount}");
        $this->info("Issues found: {$issuesFound}");
        
        if ($issuesFound > 0) {
            $this->newLine();
            $this->info('Recommendations:');
            $this->info('1. Fix dates for invoices with issues');
            $this->info('2. Consider running: php artisan invoices:fix-dates');
        } else {
            $this->newLine();
            $this->info('All date extractions look good!');
        }

        return 0;
    }

    private function extractAllDates($text)
    {
        $dates = [];
        
        // Multiple date patterns to try
        $patterns = [
            // Standard invoice date patterns
            '/(?:invoice\s*date|date|bill\s*date)[\s:#]*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/(?:invoice\s*date|date|bill\s*date)[\s:#]*([0-9]{4}[-\/][0-9]{1,2}[-\/][0-9]{1,2})/i',
            
            // Date patterns without keywords
            '/([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/',
            '/([0-9]{4}[-\/][0-9]{1,2}[-\/][0-9]{1,2})/',
            
            // Indian date formats
            '/Date\s*[:\-]?\s*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/Dated\s*[:\-]?\s*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            
            // More specific patterns
            '/([0-9]{1,2}[-](0[1-9]|1[0-2])[-][0-9]{4})/', // DD-MM-YYYY
            '/([0-9]{1,2}[\/](0[1-9]|1[0-2])[\/][0-9]{4})/', // DD/MM/YYYY
            '/([0-9]{4}[-](0[1-9]|1[0-2])[-][0-9]{1,2})/', // YYYY-MM-DD
            '/([0-9]{4}[\/](0[1-9]|1[0-2])[\/][0-9]{1,2})/', // YYYY/MM/DD
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $normalizedDate = $this->normalizeDate($match[1]);
                    if ($normalizedDate && !in_array($normalizedDate, $dates)) {
                        $dates[] = $normalizedDate;
                    }
                }
            }
        }

        return $dates;
    }

    private function selectBestDate($dates)
    {
        // Prefer more recent dates and reasonable invoice dates
        $currentDate = now();
        $bestDate = null;
        $bestScore = -1;

        foreach ($dates as $date) {
            try {
                $dateObj = new \DateTime($date);
                $daysDiff = $currentDate->diff($dateObj)->days;
                
                // Score based on recency (prefer dates within last 365 days)
                $score = 0;
                if ($daysDiff <= 365) {
                    $score = 100 - $daysDiff; // More recent = higher score
                } elseif ($daysDiff <= 1825) { // Within 5 years
                    $score = 50;
                }
                
                // Bonus for dates that look like invoice dates (not too old, not future)
                if ($daysDiff >= 0 && $daysDiff <= 365) {
                    $score += 50;
                }
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestDate = $date;
                }
            } catch (Exception $e) {
                // Skip invalid dates
            }
        }

        return $bestDate;
    }

    private function normalizeDate($date)
    {
        try {
            $date = str_replace(['st', 'nd', 'rd', 'th'], '', $date);
            
            // Try multiple date formats
            $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'Y/m/d', 'd/m/y', 'Y-m-d'];
            
            foreach ($formats as $format) {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed) {
                    return $parsed->format('Y-m-d');
                }
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
}
