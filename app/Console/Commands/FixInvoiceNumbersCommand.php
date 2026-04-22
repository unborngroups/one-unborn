<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;
use Smalot\PdfParser\Parser;

class FixInvoiceNumbersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:fix-invoice-numbers {--limit=50 : Number of invoices to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice numbers with wrong values extracted from PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Fixing Invoice Numbers ===');
        $this->info('Correcting wrong invoice numbers by extracting proper ones from PDFs.');
        $this->newLine();

        // Get invoices with problematic invoice numbers
        $limit = $this->option('limit');
        $problematicInvoices = PurchaseInvoice::query()
            ->where(function($query) {
                $query->where('invoice_no', 'UNBORN')
                      ->orWhere('invoice_no', 'like', 'Date%')
                      ->orWhere('invoice_no', 'like', 'Account%')
                      ->orWhere('invoice_no', 'like', 'along%')
                      ->orWhere('invoice_no', 'like', 'Invoice%')
                      ->orWhere('invoice_no', 'like', 'ing%')
                      ->orWhere('invoice_no', 'like', 'erred%')
                      ->orWhere('invoice_no', 'like', 'Frequency%')
                      ->orWhere('invoice_no', 'like', 'LIMITED%');
            })
            ->whereNotNull('po_invoice_file')
            ->limit($limit)
            ->get(['id', 'invoice_no', 'vendor_name', 'total_amount', 'po_invoice_file']);

        $this->info("Found " . count($problematicInvoices) . " invoices with problematic invoice numbers:");
        $this->newLine();

        $fixedCount = 0;

        foreach ($problematicInvoices as $invoice) {
            $this->info("Processing invoice: {$invoice->invoice_no}");
            $this->line("  Current Invoice No: {$invoice->invoice_no}");
            $this->line("  Vendor: {$invoice->vendor_name}");

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

                // Extract invoice numbers from PDF
                $extractedInvoiceNumbers = $this->extractAllInvoiceNumbers($text);
                
                if (empty($extractedInvoiceNumbers)) {
                    $this->warn("  - No invoice numbers found in PDF text");
                    $this->newLine();
                    continue;
                }

                $this->line("  Invoice numbers found in PDF: " . implode(', ', array_unique($extractedInvoiceNumbers)));

                // Select best invoice number
                $bestInvoiceNumber = $this->selectBestInvoiceNumber($extractedInvoiceNumbers, $text);
                
                if ($bestInvoiceNumber && $bestInvoiceNumber !== $invoice->invoice_no) {
                    $this->line("  Selected best invoice number: {$bestInvoiceNumber}");
                    
                    // Update invoice
                    $invoice->update(['invoice_no' => $bestInvoiceNumber]);
                    
                    $this->info("  - Invoice number fixed successfully!");
                    $fixedCount++;
                } else {
                    $this->info("  - Invoice number already correct or no better option found");
                }

            } catch (Exception $e) {
                $this->error("  - Error processing PDF: " . $e->getMessage());
            }

            $this->newLine();
        }

        $this->info('=== Invoice Number Fix Complete ===');
        $this->info("Total invoices processed: " . count($problematicInvoices));
        $this->info("Invoices fixed: {$fixedCount}");
        
        if ($fixedCount > 0) {
            $this->newLine();
            $this->info('Invoice number extraction issues have been resolved.');
            $this->info('Please refresh Auto Invoice Processing page to see corrected invoice numbers.');
        } else {
            $this->newLine();
            $this->info('No invoice numbers could be fixed with available PDF files.');
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
            $commonWords = ['DATE', 'TOTAL', 'AMOUNT', 'TAX', 'GST', 'DUE', 'PAID', 'NETWORKS', 'SOLUTIONS'];
            if (in_array(strtoupper($number), $commonWords)) {
                $score -= 50;
            }
            
            // Avoid GST numbers and PAN numbers
            if (preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{1}[A-Z]{1}[0-9]{1}$/', $number)) {
                $score -= 100; // GSTIN pattern
            }
            
            if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $number)) {
                $score -= 100; // PAN pattern
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestInvoiceNumber = $number;
            }
        }

        return $bestInvoiceNumber;
    }
}
