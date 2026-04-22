<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;

class CheckInvoiceStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check current status of problematic invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Checking Current Invoice Status ===');
        $this->newLine();

        // Check specific problematic invoices
        $problematicInvoices = ['GMAIL-80f0fb0abd', '635207', 'UNBORN', 'LIMITED', 'ing', '04371-299056'];
        
        $this->info('Checking specific problematic invoices:');
        $this->newLine();
        
        foreach ($problematicInvoices as $invoiceNo) {
            $invoice = PurchaseInvoice::where('invoice_no', $invoiceNo)->first();
            
            if ($invoice) {
                $this->line("Invoice: {$invoice->invoice_no}");
                $this->line("  Vendor: {$invoice->vendor_name}");
                $this->line("  Total: {$invoice->total_amount}");
                $this->line("  Confidence: {$invoice->confidence_score}%");
                $this->line("  Status: {$invoice->status}");
                $this->line("  GSTIN: " . ($invoice->gstin ?? 'N/A'));
                $this->newLine();
            } else {
                $this->warn("Invoice '{$invoiceNo}' not found in database");
                $this->newLine();
            }
        }

        // Check all invoices with low confidence or wrong status
        $this->info('=== All Invoices Needing Attention ===');
        $this->newLine();
        
        $problematicInvoices = PurchaseInvoice::query()
            ->where(function($query) {
                $query->where('confidence_score', '<', 85)
                      ->orWhere('status', 'needs_review')
                      ->orWhere('status', 'failed')
                      ->orWhere('confidence_score', null);
            })
            ->limit(10)
            ->get(['invoice_no', 'vendor_name', 'total_amount', 'confidence_score', 'status']);

        $this->info("Found " . count($problematicInvoices) . " invoices needing attention:");
        $this->newLine();
        
        foreach ($problematicInvoices as $invoice) {
            $this->line("Invoice: {$invoice->invoice_no}");
            $this->line("  Vendor: {$invoice->vendor_name}");
            $this->line("  Total: {$invoice->total_amount}");
            $this->line("  Confidence: {$invoice->confidence_score}%");
            $this->line("  Status: {$invoice->status}");
            $this->newLine();
        }

        return 0;
    }
}
