<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;
use Smalot\PdfParser\Parser;

class CheckInvoiceNumberExtractionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-invoice-numbers {--limit=20 : Number of invoices to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoice number extraction accuracy from invoice PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Checking Invoice Number Extraction from PDFs ===');
        $this->newLine();

        // Get recent invoices to check
        $limit = $this->option('limit');
        $invoices = PurchaseInvoice::whereNotNull('po_invoice_file')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'invoice_no', 'vendor_name', 'total_amount', 'po_invoice_file']);

        $this->info("Checking " . count($invoices) . " recent invoices for invoice number extraction accuracy:");
        $this->newLine();

        $issuesFound = 0;
        $checkedCount = 0;

        foreach ($invoices as $invoice) {
            $checkedCount++;
            $this->info("Checking invoice #{$checkedCount}: {$invoice->invoice_no}");
            $this->line("  Current Invoice No: {$invoice->invoice_no}");
            $this->line("  Vendor: {$invoice->vendor_name}");
            $this->line("  Total: {$invoice->total_amount}");

            // Get PDF file path
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

                // Extract invoice numbers using enhanced patterns
                $extractedInvoiceNumbers = $this->extractAllInvoiceNumbers($text);
                
                $this->line("  Invoice numbers found in PDF: " . implode(', ', array_unique($extractedInvoiceNumbers)));
                
                // Check if current invoice number matches any extracted number
                $invoiceNumberMatch = false;
                if ($invoice->invoice_no) {
                    foreach ($extractedInvoiceNumbers as $extractedNumber) {
                        if ($extractedNumber === $invoice->invoice_no || 
                            strpos($extractedNumber, $invoice->invoice_no) !== false ||
                            strpos($invoice->invoice_no, $extractedNumber) !== false) {
                            $invoiceNumberMatch = true;
                            break;
                        }
                    }
                }

                if ($invoiceNumberMatch) {
                    $this->info("  - Invoice number extraction: CORRECT");
                } else {
                    $this->warn("  - Invoice number extraction: ISSUE DETECTED");
                    $issuesFound++;
                    
                    if (!$invoice->invoice_no) {
                        $this->warn("    - No invoice number stored in database");
                    } else {
                        $this->warn("    - Stored invoice number '{$invoice->invoice_no}' not found in PDF");
                    }
                    
                    // Try to find best invoice number
                    if (!empty($extractedInvoiceNumbers)) {
                        $bestInvoiceNumber = $this->selectBestInvoiceNumber($extractedInvoiceNumbers, $text);
                        $this->line("    - Suggested fix: {$bestInvoiceNumber}");
                    }
                }

            } catch (Exception $e) {
                $this->error("  - Error processing PDF: " . $e->getMessage());
                $issuesFound++;
            }

            $this->newLine();
        }

        $this->info('=== Invoice Number Extraction Check Complete ===');
        $this->info("Invoices checked: {$checkedCount}");
        $this->info("Issues found: {$issuesFound}");
        
        if ($issuesFound > 0) {
            $this->newLine();
            $this->info('Recommendations:');
            $this->info('1. Fix invoice numbers for invoices with issues');
            $this->info('2. Consider running: php artisan invoices:fix-invoice-numbers');
        } else {
            $this->newLine();
            $this->info('All invoice number extractions look good!');
        }

        return 0;
    }

    private function findPdfFile($poInvoiceFile)
    {
        // Try standard path first
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

    private function extractAllInvoiceNumbers($text)
    {
        $invoiceNumbers = [];
        
        // Enhanced invoice number patterns for Indian invoices
        $patterns = [
            // Invoice number with various keywords
            '/(?:invoice\s*no|invoice\s*number|bill\s*no|bill\s*number|receipt\s*no|receipt\s*number)[\s:#]*([A-Z0-9\/\-]{5,30})/i',
            '/(?:inv\s*no|inv\s*number|bill\s*no|bill\s*number)[\s:#]*([A-Z0-9\/\-]{5,30})/i',
            
            // Invoice number patterns without keywords
            '/\b([A-Z]{2,10}[0-9]{6,15})\b/', // Like SAPR27005436478
            '/\b([A-Z]{3,6}[0-9]{8,12})\b/', // Like SKAR27007879294
            '/\b([A-Z]{4,8}[0-9]{6,10})\b/', // Like STNR27015015692
            
            // Number with hyphens and slashes
            '/\b([0-9]{3,10}[-\/][0-9]{3,10}[-\/][0-9]{3,10})\b/', // Like 04371-299056
            '/\b([0-9]{6,15}[-\/][0-9]{6,15})\b/', // Like 3212600000261421
            
            // Specific patterns for known formats
            '/\b(SAPR[0-9]{8,15})\b/', // SAPR format
            '/\b(STNR[0-9]{8,15})\b/', // STNR format
            '/\b(SKAR[0-9]{8,15})\b/', // SKAR format
            '/\b(SIPL[0-9\/\-]{8,20})\b/', // SIPL format
            
            // General alphanumeric patterns
            '/\b([A-Z0-9]{8,25})\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $invoiceNumber = trim($match[1] ?? $match[0] ?? '');
                    if ($invoiceNumber && !in_array($invoiceNumber, $invoiceNumbers)) {
                        $invoiceNumbers[] = $invoiceNumber;
                    }
                }
            }
        }

        return $invoiceNumbers;
    }

    private function selectBestInvoiceNumber($invoiceNumbers, $text)
    {
        // Look for invoice number context in text
        $invoiceNumberPatterns = [
            '/invoice\s*no.*?([A-Z0-9\/\-]{5,30})/i',
            '/bill\s*no.*?([A-Z0-9\/\-]{5,30})/i',
            '/inv\s*no.*?([A-Z0-9\/\-]{5,30})/i',
        ];

        foreach ($invoiceNumberPatterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                $contextInvoiceNumber = trim($match[1]);
                if ($contextInvoiceNumber && in_array($contextInvoiceNumber, $invoiceNumbers)) {
                    return $contextInvoiceNumber;
                }
            }
        }

        // If no clear invoice number context, prefer longer alphanumeric numbers
        $bestInvoiceNumber = null;
        $bestScore = -1;

        foreach ($invoiceNumbers as $number) {
            $score = 0;
            
            // Prefer longer numbers
            $score += strlen($number);
            
            // Prefer alphanumeric over numeric only
            if (preg_match('/[A-Z]/', $number)) {
                $score += 10;
            }
            
            // Prefer common patterns (SAPR, STNR, SKAR, SIPL)
            if (preg_match('/^(SAPR|STNR|SKAR|SIPL)/', $number)) {
                $score += 20;
            }
            
            // Avoid very short numbers
            if (strlen($number) < 8) {
                $score -= 10;
            }
            
            // Avoid common words
            $commonWords = ['DATE', 'TOTAL', 'AMOUNT', 'TAX', 'GST', 'DUE', 'PAID'];
            if (in_array(strtoupper($number), $commonWords)) {
                $score -= 50;
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestInvoiceNumber = $number;
            }
        }

        return $bestInvoiceNumber;
    }
}
