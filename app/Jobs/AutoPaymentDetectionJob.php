<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentService;
use App\Services\RazorpayService;
use App\Models\Company;

class AutoPaymentDetectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 1800; // 30 minutes

    public function __construct()
    {}

    public function handle(PaymentService $paymentService): void
    {
        Log::info('Starting auto payment detection job');

        try {
            // Get all companies with auto-payment enabled
            $companies = Company::whereHas('vendors', function ($query) {
                $query->where('auto_payment_enabled', true);
            })->get();

            foreach ($companies as $company) {
                $this->processCompanyPayments($company, $paymentService);
            }

            Log::info('Auto payment detection job completed', [
                'companies_processed' => $companies->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Auto payment detection job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function processCompanyPayments(Company $company, PaymentService $paymentService): void
    {
        Log::info('Processing company payments', [
            'company_id' => $company->id,
            'company_name' => $company->company_name,
        ]);

        try {
            // Find invoices ready for payment
            $invoices = $paymentService->findInvoicesReadyForPayment($company->id);

            if ($invoices->isEmpty()) {
                Log::info('No invoices ready for payment', [
                    'company_id' => $company->id,
                ]);
                return;
            }

            // Group invoices by vendor and apply monthly limits
            $groupedInvoices = $this->groupInvoicesByVendor($invoices);

            foreach ($groupedInvoices as $vendorId => $vendorInvoices) {
                $this->processVendorPayments($company, $vendorId, $vendorInvoices, $paymentService);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process company payments', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function groupInvoicesByVendor($invoices): array
    {
        $grouped = [];
        $vendorMonthlySpend = [];

        foreach ($invoices as $invoice) {
            $vendorId = $invoice->vendor_id;
            $vendor = $invoice->vendor;
            
            // Check monthly limit
            $currentMonthSpend = $vendorMonthlySpend[$vendorId] ?? 0;
            $monthlyLimit = $vendor->monthly_payment_limit ?? 500000;
            
            if ($currentMonthSpend + $invoice->grand_total <= $monthlyLimit) {
                $grouped[$vendorId][] = $invoice;
                $vendorMonthlySpend[$vendorId] += $invoice->grand_total;
            } else {
                Log::info('Invoice skipped due to monthly limit', [
                    'invoice_id' => $invoice->id,
                    'vendor_id' => $vendorId,
                    'invoice_amount' => $invoice->grand_total,
                    'current_monthly_spend' => $currentMonthSpend,
                    'monthly_limit' => $monthlyLimit,
                ]);
            }
        }

        return $grouped;
    }

    private function processVendorPayments(Company $company, int $vendorId, array $invoices, PaymentService $paymentService): void
    {
        if (empty($invoices)) {
            return;
        }

        try {
            // Create payment batch for vendor invoices
            $invoiceIds = array_map(fn($invoice) => $invoice->id, $invoices);
            $batch = $paymentService->createPaymentBatch($invoiceIds, $company->id);

            // Submit for approval (auto-approve if configured)
            $vendor = $invoices[0]->vendor;
            if ($vendor->auto_payment_enabled) {
                // Auto-approve for trusted vendors
                $paymentService->approvePaymentBatch($batch, 1, 'Auto-approved payment batch');
                
                // Dispatch processing job
                ProcessPaymentBatchJob::dispatch($batch, $company->id)
                    ->onQueue('payments')
                    ->delay(now()->addMinutes(5)); // 5 minute delay for any manual review

                Log::info('Payment batch auto-approved and queued', [
                    'batch_id' => $batch->id,
                    'vendor_id' => $vendorId,
                    'invoice_count' => count($invoices),
                    'total_amount' => $batch->total_amount,
                ]);
            } else {
                // Submit for manual approval
                $paymentService->submitForApproval($batch, 1);

                Log::info('Payment batch submitted for approval', [
                    'batch_id' => $batch->id,
                    'vendor_id' => $vendorId,
                    'invoice_count' => count($invoices),
                    'total_amount' => $batch->total_amount,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process vendor payments', [
                'company_id' => $company->id,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
