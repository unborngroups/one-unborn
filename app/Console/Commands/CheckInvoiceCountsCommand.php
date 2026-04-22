<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;

class CheckInvoiceCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check current invoice counts in auto invoice and failed pages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Current Invoice Status Report ===');
        $this->newLine();

        // Get mail read days to match the same filter as the controller
        $mailReadDays = 30; // Default value
        $fromDate = now()->subDays($mailReadDays)->startOfDay();

        $this->info("Using date filter from: {$fromDate}");
        $this->newLine();

        // Check auto invoice page count (same query as autoInvoiceIndex)
        $autoInvoiceQuery = PurchaseInvoice::with(['vendor', 'emailLog'])
            ->whereNotNull('email_log_id')
            ->where('created_at', '>=', $fromDate)
            ->whereIn('status', ['draft', 'needs_review', 'verified'])
            ->latest();

        // Apply the same duplicate removal logic
        $autoInvoiceQuery->whereRaw('id IN (
            SELECT MAX(id) FROM purchase_invoices 
            WHERE created_at >= ? AND email_log_id IS NOT NULL
            GROUP BY 
                CASE 
                    WHEN invoice_no IS NOT NULL AND invoice_no != "" THEN invoice_no 
                    ELSE CONCAT(
                        COALESCE(vendor_gstin, ""), "_",
                        COALESCE(vendor_name, ""), "_", 
                        COALESCE(DATE(invoice_date), ""), "_",
                        COALESCE(total_amount, ""), "_",
                        COALESCE(grand_total, "")
                    )
                END
        )', [$fromDate]);

        $autoInvoices = $autoInvoiceQuery->get();
        $autoInvoiceCount = count($autoInvoices);

        $this->info('=== Auto Invoice Page ===');
        $this->info("Total invoices: {$autoInvoiceCount}");
        $this->newLine();

        // Break down by status
        $autoStatusBreakdown = $autoInvoices->groupBy('status');
        foreach ($autoStatusBreakdown as $status => $invoices) {
            $this->line("  {$status}: " . count($invoices) . " invoices");
        }
        $this->newLine();

        // Show top 5 invoices from auto invoice page
        $this->info('Top 5 Auto Invoices:');
        foreach ($autoInvoices->take(5) as $invoice) {
            $this->line("  {$invoice->invoice_no} | {$invoice->vendor_name} | {$invoice->total_amount} | {$invoice->confidence_score}% | {$invoice->status}");
        }
        $this->newLine();

        // Check failed invoices page count
        $failedInvoices = PurchaseInvoice::where(function ($query) {
            $query->where('status', 'failed')
                  ->orWhereNotNull('raw_json->import_failure_reason')
                  ->orWhereNotNull('raw_json->parse_error');
        })->where('created_at', '>=', $fromDate)->get();

        $failedInvoiceCount = count($failedInvoices);

        $this->info('=== Failed Invoices Page ===');
        $this->info("Total invoices: {$failedInvoiceCount}");
        $this->newLine();

        // Show top 5 failed invoices
        $this->info('Top 5 Failed Invoices:');
        foreach ($failedInvoices->take(5) as $invoice) {
            $failureReason = $invoice->raw_json['import_failure_reason'] ?? $invoice->raw_json['parse_error'] ?? 'Unknown';
            $this->line("  {$invoice->invoice_no} | {$invoice->vendor_name} | {$invoice->total_amount} | {$invoice->confidence_score}% | {$failureReason}");
        }
        $this->newLine();

        // Summary comparison
        $this->info('=== Summary ===');
        $this->info("User reported: 22 auto invoices, 14 failed invoices");
        $this->info("System shows: {$autoInvoiceCount} auto invoices, {$failedInvoiceCount} failed invoices");
        $this->newLine();

        if ($autoInvoiceCount == 22 && $failedInvoiceCount == 14) {
            $this->info('Perfect! Counts match exactly.');
        } else {
            $this->info('Counts differ. This could be due to:');
            $this->info('- Different date filters');
            $this->info('- Recent invoice additions/removals');
            $this->info('- Cache updates');
        }

        // Check for any remaining issues
        $this->newLine();
        $this->info('=== Quality Check ===');
        
        $lowConfidenceAuto = $autoInvoices->where('confidence_score', '<', 70)->count();
        $lowConfidenceFailed = $failedInvoices->where('confidence_score', '<', 70)->count();

        $this->info("Auto invoices with <70% confidence: {$lowConfidenceAuto}");
        $this->info("Failed invoices with <70% confidence: {$lowConfidenceFailed}");

        if ($lowConfidenceAuto > 0 || $lowConfidenceFailed > 0) {
            $this->newLine();
            $this->info('Some invoices still need attention. Consider running:');
            $this->info('php artisan invoices:fix-data --limit=50');
        } else {
            $this->newLine();
            $this->info('All invoices have good confidence scores!');
        }

        return 0;
    }
}
