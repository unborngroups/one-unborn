<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;
use Smalot\PdfParser\Parser;

class FixInvoiceDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:fix-dates {--limit=50 : Number of invoices to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice dates with wrong values (1970-01-01) and extract correct dates from PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Fixing Invoice Dates ===');
        $this->info('Correcting wrong dates (1970-01-01) and extracting proper dates from PDFs.');
        $this->newLine();

        // Get invoices with date issues
        $limit = $this->option('limit');
        $problematicInvoices = PurchaseInvoice::query()
            ->where(function($query) {
                $query->where('invoice_date', '1970-01-01')
                      ->orWhereNull('invoice_date')
                      ->orWhere('invoice_date', 'like', '1970-%');
            })
            ->whereNotNull('po_invoice_file')
            ->limit($limit)
            ->get(['id', 'invoice_no', 'invoice_date', 'vendor_name', 'total_amount', 'po_invoice_file']);

        $this->info("Found " . count($problematicInvoices) . " invoices with date issues:");
        $this->newLine();

        $fixedCount = 0;

        foreach ($problematicInvoices as $invoice) {
            $this->info("Processing invoice: {$invoice->invoice_no}");
            $this->line("  Current Date: " . ($invoice->invoice_date ?? 'NULL'));
            $this->line("  PDF File: {$invoice->po_invoice_file}");

            // Try to find the PDF file
            $pdfPath = $this->findPdfFile($invoice->po_invoice_file);
            
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

                // Extract dates from PDF
                $extractedDates = $this->extractDatesFromText($text);
                
                if (empty($extractedDates)) {
                    $this->warn("  - No dates found in PDF text");
                    $this->newLine();
                    continue;
                }

                $this->line("  Dates found in PDF: " . implode(', ', $extractedDates));

                // Select the best date
                $bestDate = $this->selectBestInvoiceDate($extractedDates, $text);
                
                if ($bestDate) {
                    $this->line("  Selected best date: {$bestDate}");
                    
                    // Update the invoice
                    $invoice->update(['invoice_date' => $bestDate]);
                    
                    $this->info("  - Date fixed successfully!");
                    $fixedCount++;
                } else {
                    $this->warn("  - Could not determine best date");
                }

            } catch (Exception $e) {
                $this->error("  - Error processing PDF: " . $e->getMessage());
            }

            $this->newLine();
        }

        $this->info('=== Date Fix Complete ===');
        $this->info("Total invoices processed: " . count($problematicInvoices));
        $this->info("Invoices fixed: {$fixedCount}");
        
        if ($fixedCount > 0) {
            $this->newLine();
            $this->info('Date extraction issues have been resolved.');
            $this->info('Please refresh the Auto Invoice Processing page to see the corrected dates.');
        } else {
            $this->newLine();
            $this->info('No dates could be fixed with the available PDF files.');
        }

        return 0;
    }

    private function findPdfFile($poInvoiceFile)
    {
        // Try the standard path first
        $standardPath = public_path('images/poinvoice_files/' . $poInvoiceFile);
        if (file_exists($standardPath)) {
            return $standardPath;
        }

        // Try to find by invoice number in filename
        $pdfDir = public_path('images/poinvoice_files/');
        $files = scandir($pdfDir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $filePath = $pdfDir . $file;
            if (is_file($filePath)) {
                // Check if invoice number is in filename
                if (strpos($file, str_replace(['/', '-'], '_', $poInvoiceFile)) !== false) {
                    return $filePath;
                }
            }
        }

        return null;
    }

    private function extractDatesFromText($text)
    {
        $dates = [];
        
        // Enhanced date patterns for Indian invoices
        $patterns = [
            // Invoice date with various keywords (including month names)
            '/(?:invoice\s*date|date|bill\s*date|due\s*date)[\s:#]*([0-9]{1,2}[-\/](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-\/][0-9]{4})/i',
            '/(?:invoice\s*date|date|bill\s*date|due\s*date)[\s:#]*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/(?:invoice\s*date|date|bill\s*date|due\s*date)[\s:#]*([0-9]{4}[-\/](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-\/][0-9]{1,2})/i',
            '/(?:invoice\s*date|date|bill\s*date|due\s*date)[\s:#]*([0-9]{4}[-\/][0-9]{1,2}[-\/][0-9]{1,2})/i',
            
            // Date patterns without keywords (including month names)
            '/\b([0-9]{1,2}[-](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-][0-9]{4})\b/', // DD-MMM-YYYY
            '/\b([0-9]{1,2}[-](0[1-9]|1[0-2])[-][0-9]{4})\b/', // DD-MM-YYYY
            '/\b([0-9]{1,2}[\/](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[\/][0-9]{4})\b/', // DD/MMM/YYYY
            '/\b([0-9]{1,2}[\/](0[1-9]|1[0-2])[\/][0-9]{4})\b/', // DD/MM/YYYY
            '/\b([0-9]{4}[-](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-][0-9]{1,2})\b/', // YYYY-MMM-DD
            '/\b([0-9]{4}[-](0[1-9]|1[0-2])[-][0-9]{1,2})\b/', // YYYY-MM-DD
            '/\b([0-9]{4}[\/](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[\/][0-9]{1,2})\b/', // YYYY/MMM/DD
            '/\b([0-9]{4}[\/](0[1-9]|1[0-2])[\/][0-9]{1,2})\b/', // YYYY/MM/DD
            
            // Indian format specific
            '/Date\s*[:\-]?\s*([0-9]{1,2}[-\/](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-\/][0-9]{4})/i',
            '/Date\s*[:\-]?\s*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/Dated\s*[:\-]?\s*([0-9]{1,2}[-\/](?:JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[-\/][0-9]{4})/i',
            '/Dated\s*[:\-]?\s*([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            
            // From filenames (for aspirare files)
            '/\([0-9]{1,2}[-][0-9]{1,2}[-][0-9]{4}\s*to\s*[0-9]{1,2}[-][0-9]{1,2}[-][0-9]{4}\)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $dateStr = $match[1] ?? null;
                    if ($dateStr) {
                        $normalizedDate = $this->normalizeDate($dateStr);
                        if ($normalizedDate && !in_array($normalizedDate, $dates)) {
                            $dates[] = $normalizedDate;
                        }
                    }
                }
            }
        }

        return $dates;
    }

    private function selectBestInvoiceDate($dates, $text)
    {
        if (empty($dates)) {
            return null;
        }

        // Look for invoice date context in text
        $invoiceDatePatterns = [
            '/invoice\s*date.*?([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
            '/bill\s*date.*?([0-9]{1,2}[-\/][0-9]{1,2}[-\/][0-9]{4})/i',
        ];

        foreach ($invoiceDatePatterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                $contextDate = $this->normalizeDate($match[1]);
                if ($contextDate && in_array($contextDate, $dates)) {
                    return $contextDate;
                }
            }
        }

        // If no clear invoice date context, prefer most recent reasonable date
        $currentDate = now();
        $bestDate = null;
        $bestScore = -1;

        foreach ($dates as $date) {
            try {
                $dateObj = new \DateTime($date);
                $daysDiff = abs($currentDate->diff($dateObj)->days);
                
                // Score based on recency (prefer dates within last 365 days)
                $score = 0;
                if ($daysDiff <= 365) {
                    $score = 100 - $daysDiff;
                } elseif ($daysDiff <= 1825) { // Within 5 years
                    $score = 50;
                }
                
                // Avoid future dates
                if ($dateObj > $currentDate) {
                    $score -= 50;
                }
                
                // Avoid very old dates
                if ($daysDiff > 3650) { // More than 10 years
                    $score -= 100;
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
            
            // Try multiple date formats including month names
            $formats = [
                'd/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'Y/m/d', 
                'd/m/y', 'Y-m-d', 'd-M-Y', 'd-M-y',
                'd-M-Y', 'd/m/Y', 'd-M-Y', 'd-M-y',
                'd-M-Y', 'd-M-y', 'd-M-Y', 'd-M-y',
                'd-M-Y', 'd-M-y', 'd-M-Y', 'd-M-y'
            ];
            
            // Add month name formats
            $monthFormats = [
                'd-M-Y', // 02-APR-2026
                'd-M-y', // 02-APR-26
                'd/M/Y', // 02/APR/2026
                'd/M/y', // 02/APR/26
                'M-d-Y', // APR-02-2026
                'M/d/Y', // APR/02/2026
                'Y-M-d', // 2026-APR-02
                'Y/M/d', // 2026/APR/02
            ];
            
            $allFormats = array_merge($formats, $monthFormats);
            
            foreach ($allFormats as $format) {
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
