<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Vendor;
use App\Models\PurchaseInvoice;
use App\Models\PaymentBatch;
use App\Models\PaymentTransaction;
use App\Models\Company;
use App\Models\FinanceApproval;
use App\Models\FinanceAuditLog;

class PaymentService
{
    private RazorpayService $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Calculate due date based on vendor payment terms
     */
    public function calculateDueDate(PurchaseInvoice $invoice): Carbon
    {
        $vendor = $invoice->vendor;
        $paymentTermsDays = $vendor->payment_terms_days ?? 15;
        
        return $invoice->invoice_date->addDays($paymentTermsDays);
    }

    /**
     * Find invoices ready for payment
     */
    public function findInvoicesReadyForPayment(int $companyId): \Illuminate\Support\Collection
    {
        return PurchaseInvoice::with(['vendor'])
            ->where('company_id', $companyId)
            ->where('payment_status', 'pending')
            ->where('auto_payment_enabled', true)
            ->where('due_date', '<=', now())
            ->whereHas('vendor', function ($query) {
                $query->where('auto_payment_enabled', true);
            })
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Create payment batch for invoices
     */
    public function createPaymentBatch(array $invoiceIds, int $companyId): PaymentBatch
    {
        return DB::transaction(function () use ($invoiceIds, $companyId) {
            $invoices = PurchaseInvoice::whereIn('id', $invoiceIds)
                ->where('company_id', $companyId)
                ->where('payment_status', 'pending')
                ->lockForUpdate()
                ->get();

            if ($invoices->isEmpty()) {
                throw new \Exception('No valid invoices found for payment batch');
            }

            $totalAmount = $invoices->sum('grand_total');
            $batchReference = 'PAY-' . date('YmdHis') . '-' . str_pad($companyId, 3, '0', STR_PAD_LEFT);

            // Create payment batch
            $batch = PaymentBatch::create([
                'company_id' => $companyId,
                'batch_reference' => $batchReference,
                'total_amount' => $totalAmount,
                'total_invoices' => $invoices->count(),
                'status' => 'pending',
            ]);

            // Update invoices with batch ID
            foreach ($invoices as $invoice) {
                $invoice->update([
                    'payment_batch_id' => $batch->id,
                    'payment_status' => 'processing',
                ]);
            }

            // Create payment transactions
            foreach ($invoices as $invoice) {
                PaymentTransaction::create([
                    'payment_batch_id' => $batch->id,
                    'purchase_invoice_id' => $invoice->id,
                    'razorpay_payment_id' => $batchReference . '-INV-' . $invoice->id,
                    'amount' => $invoice->grand_total,
                    'status' => 'created',
                ]);
            }

            Log::info('Payment batch created', [
                'batch_id' => $batch->id,
                'batch_reference' => $batchReference,
                'total_amount' => $totalAmount,
                'invoice_count' => $invoices->count(),
            ]);

            return $batch->load('paymentTransactions.purchaseInvoice.vendor');
        });
    }

    /**
     * Process payment batch through Razorpay
     */
    public function processPaymentBatch(PaymentBatch $batch): array
    {
        if (!$batch->canBeProcessed()) {
            throw new \Exception('Payment batch cannot be processed');
        }

        // Check balance
        if (!$this->razorpayService->hasSufficientBalance($batch->total_amount)) {
            throw new \Exception('Insufficient Razorpay balance for payment batch');
        }

        $batch->update([
            'status' => 'processing',
            'processing_notes' => 'Processing payouts through Razorpay',
        ]);

        $payouts = [];
        foreach ($batch->paymentTransactions as $transaction) {
            $invoice = $transaction->purchaseInvoice;
            $vendor = $invoice->vendor;

            $payouts[] = [
                'account_number' => $vendor->bank_account_no,
                'beneficiary_name' => $vendor->vendor_name,
                'ifsc_code' => $vendor->ifsc_code,
                'amount' => $transaction->amount,
                'reference_id' => $transaction->razorpay_payment_id,
                'narration' => "Payment for Invoice {$invoice->invoice_no}",
                'notes' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_no,
                    'vendor_id' => $vendor->id,
                    'batch_reference' => $batch->batch_reference,
                ],
            ];

            // Mark transaction as processing
            $transaction->markAsProcessing();
        }

        try {
            // Process payouts with Razorpay
            $results = $this->razorpayService->createBatchPayouts($payouts);
            
            // Update transactions with results
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($results as $index => $result) {
                $transaction = $batch->paymentTransactions[$index];
                
                if ($result['success']) {
                    $transaction->markAsCompleted(
                        $result['data']['id'] ?? null,
                        $result['data']
                    );
                    $successCount++;
                    
                    // Update invoice status
                    $transaction->purchaseInvoice->update([
                        'payment_status' => 'completed',
                        'razorpay_payment_id' => $result['data']['id'] ?? null,
                        'paid_amount' => $transaction->amount,
                        'payment_processed_at' => now(),
                    ]);
                } else {
                    $transaction->markAsFailed(
                        $result['error'] ?? 'Unknown error',
                        $result
                    );
                    $failureCount++;
                    
                    // Update invoice status
                    $transaction->purchaseInvoice->update([
                        'payment_status' => 'failed',
                        'payment_failure_reason' => $result['error'] ?? 'Unknown error',
                    ]);
                }
            }

            // Update batch status
            $batch->update([
                'status' => $failureCount === 0 ? 'completed' : 'completed_with_failures',
                'processed_at' => now(),
                'processing_notes' => "Processed: {$successCount} successful, {$failureCount} failed",
            ]);

            Log::info('Payment batch processed', [
                'batch_id' => $batch->id,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
            ]);

            return [
                'success' => true,
                'batch_id' => $batch->id,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'results' => $results,
            ];

        } catch (\Exception $e) {
            // Mark all transactions as failed
            foreach ($batch->paymentTransactions as $transaction) {
                $transaction->markAsFailed($e->getMessage());
            }

            $batch->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            Log::error('Payment batch processing failed', [
                'batch_id' => $batch->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Submit payment batch for accountant approval
     */
    public function submitForAccountantApproval(PaymentBatch $batch, int $userId): void
    {
        if ($batch->status !== 'pending') {
            throw new \Exception('Only pending batches can be submitted for approval');
        }

        DB::transaction(function () use ($batch, $userId) {
            $batch->update([
                'status' => 'accountant_approval',
            ]);

            // Create accountant approval workflow
            \App\Models\PaymentApprovalWorkflow::create([
                'payment_batch_id' => $batch->id,
                'approval_level' => 'accountant',
                'status' => 'pending',
                'remarks' => "Payment batch {$batch->batch_reference} submitted for accountant approval",
            ]);

            // Create finance approval record for tracking
            FinanceApproval::create([
                'model_type' => PaymentBatch::class,
                'model_id' => $batch->id,
                'action' => 'payment_batch_accountant_approval',
                'maker_id' => $userId,
                'status' => 'pending',
                'approval_level' => 'accountant',
                'remarks' => "Payment batch {$batch->batch_reference} submitted for accountant approval",
            ]);

            // Log audit
            $this->logAudit($batch, 'submit_for_accountant_approval', $userId, [], [
                'status' => 'accountant_approval',
            ]);
        });
    }

    /**
     * Approve payment batch at accountant level
     */
    public function approveAccountantLevel(PaymentBatch $batch, int $userId, string $remarks = null): void
    {
        if ($batch->status !== 'accountant_approval') {
            throw new \Exception('Only batches in accountant approval can be approved');
        }

        DB::transaction(function () use ($batch, $userId, $remarks) {
            // Update accountant approval workflow
            $accountantApproval = $batch->approvalWorkflows()
                ->accountantLevel()
                ->pending()
                ->firstOrFail();
            
            $accountantApproval->markAsApproved($userId, $remarks);

            // Update batch status to finance manager approval
            $batch->update([
                'status' => 'finance_manager_approval',
                'accountant_approved_at' => now(),
                'accountant_approved_by' => $userId,
            ]);

            // Update finance approval record
            FinanceApproval::where([
                'model_type' => PaymentBatch::class,
                'model_id' => $batch->id,
                'approval_level' => 'accountant',
                'status' => 'pending',
            ])->update([
                'checker_id' => $userId,
                'status' => 'approved',
                'remarks' => $remarks,
            ]);

            // Create finance manager approval workflow
            \App\Models\PaymentApprovalWorkflow::create([
                'payment_batch_id' => $batch->id,
                'approval_level' => 'finance_manager',
                'status' => 'pending',
                'remarks' => "Payment batch {$batch->batch_reference} ready for finance manager approval",
            ]);

            // Log audit
            $this->logAudit($batch, 'accountant_approve', $userId, [], [
                'accountant_approved_by' => $userId,
                'accountant_approved_at' => now(),
                'remarks' => $remarks,
            ]);
        });
    }

    /**
     * Approve payment batch at finance manager level
     */
    public function approveFinanceManagerLevel(PaymentBatch $batch, int $userId, string $remarks = null): void
    {
        if ($batch->status !== 'finance_manager_approval') {
            throw new \Exception('Only batches in finance manager approval can be approved');
        }

        DB::transaction(function () use ($batch, $userId, $remarks) {
            // Update finance manager approval workflow
            $financeApproval = $batch->approvalWorkflows()
                ->financeManagerLevel()
                ->pending()
                ->firstOrFail();
            
            $financeApproval->markAsApproved($userId, $remarks);

            // Update batch status to pending processing
            $batch->update([
                'status' => 'pending',
                'finance_manager_approved_at' => now(),
                'finance_manager_approved_by' => $userId,
                'approved_at' => now(),
                'approved_by' => $userId,
            ]);

            // Update finance approval record
            FinanceApproval::where([
                'model_type' => PaymentBatch::class,
                'model_id' => $batch->id,
                'approval_level' => 'finance_manager',
                'status' => 'pending',
            ])->update([
                'checker_id' => $userId,
                'status' => 'approved',
                'remarks' => $remarks,
            ]);

            // Log audit
            $this->logAudit($batch, 'finance_manager_approve', $userId, [], [
                'finance_manager_approved_by' => $userId,
                'finance_manager_approved_at' => now(),
                'approved_at' => now(),
                'approved_by' => $userId,
                'remarks' => $remarks,
            ]);
        });
    }

    /**
     * Reject payment batch at any level
     */
    public function rejectPaymentBatch(PaymentBatch $batch, int $userId, string $remarks, string $level): void
    {
        DB::transaction(function () use ($batch, $userId, $remarks, $level) {
            // Update the specific level approval workflow
            $approval = $batch->approvalWorkflows()
                ->where('approval_level', $level)
                ->pending()
                ->firstOrFail();
            
            $approval->markAsRejected($remarks);

            // Update batch status to cancelled
            $batch->update([
                'status' => 'cancelled',
                'failure_reason' => "Rejected at {$level} level: {$remarks}",
            ]);

            // Update finance approval record
            FinanceApproval::where([
                'model_type' => PaymentBatch::class,
                'model_id' => $batch->id,
                'approval_level' => $level,
                'status' => 'pending',
            ])->update([
                'checker_id' => $userId,
                'status' => 'rejected',
                'remarks' => $remarks,
            ]);

            // Reset invoice statuses
            foreach ($batch->paymentTransactions as $transaction) {
                $transaction->purchaseInvoice->update([
                    'payment_status' => 'pending',
                    'payment_batch_id' => null,
                ]);
            }

            // Delete transactions
            $batch->paymentTransactions()->delete();

            // Log audit
            $this->logAudit($batch, 'reject', $userId, [], [
                'rejected_by' => $userId,
                'rejected_level' => $level,
                'remarks' => $remarks,
            ]);
        });
    }

    /**
     * Check if batch can be processed (has both approvals)
     */
    public function canProcessBatch(PaymentBatch $batch): bool
    {
        // Check if both accountant and finance manager approvals exist and are approved
        $accountantApproval = $batch->approvalWorkflows()
            ->accountantLevel()
            ->approved()
            ->first();
            
        $financeManagerApproval = $batch->approvalWorkflows()
            ->financeManagerLevel()
            ->approved()
            ->first();

        return $accountantApproval && $financeManagerApproval;
    }

    /**
     * Approve payment batch
     */
    public function approvePaymentBatch(PaymentBatch $batch, int $userId, string $remarks = null): void
    {
        if ($batch->status !== 'pending_approval') {
            throw new \Exception('Only batches pending approval can be approved');
        }

        DB::transaction(function () use ($batch, $userId, $remarks) {
            $batch->update([
                'status' => 'pending',
                'approved_at' => now(),
                'approved_by' => $userId,
            ]);

            // Update finance approval
            $approval = FinanceApproval::where([
                'model_type' => PaymentBatch::class,
                'model_id' => $batch->id,
                'status' => 'pending',
            ])->first();

            if ($approval) {
                $approval->update([
                    'checker_id' => $userId,
                    'status' => 'approved',
                    'remarks' => $remarks,
                ]);
            }

            // Log audit
            $this->logAudit($batch, 'approve', $userId, [], [
                'approved_by' => $userId,
                'approved_at' => now(),
                'remarks' => $remarks,
            ]);
        });
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(int $companyId, string $period = 'month'): array
    {
        $startDate = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $invoices = PurchaseInvoice::where('company_id', $companyId)
            ->where('due_date', '>=', $startDate)
            ->get();

        return [
            'total_invoices' => $invoices->count(),
            'pending_amount' => $invoices->where('payment_status', 'pending')->sum('grand_total'),
            'paid_amount' => $invoices->where('payment_status', 'completed')->sum('paid_amount'),
            'overdue_count' => $invoices->filter(fn($inv) => $inv->isOverdue())->count(),
            'overdue_amount' => $invoices->filter(fn($inv) => $inv->isOverdue())->sum('grand_total'),
            'auto_payment_enabled' => $invoices->where('auto_payment_enabled', true)->count(),
        ];
    }

    /**
     * Log audit trail
     */
    private function logAudit(PaymentBatch $batch, string $action, int $userId, array $old = [], array $new = []): void
    {
        FinanceAuditLog::create([
            'model_type' => PaymentBatch::class,
            'model_id' => $batch->id,
            'user_id' => $userId,
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Export payment data for Excel
     */
    public function exportPaymentData(PaymentBatch $batch): array
    {
        return $batch->paymentTransactions->map(function ($transaction) use ($batch) {
            $invoice = $transaction->purchaseInvoice;
            $vendor = $invoice->vendor;
            
            return [
                'Batch Reference' => $batch->batch_reference,
                'Invoice Number' => $invoice->invoice_no,
                'Invoice Date' => $invoice->invoice_date->format('Y-m-d'),
                'Due Date' => $invoice->due_date->format('Y-m-d'),
                'Vendor Name' => $vendor->vendor_name,
                'Vendor GSTIN' => $vendor->gstin,
                'Bank Account' => $vendor->bank_account_no,
                'IFSC Code' => $vendor->ifsc_code,
                'Amount' => $transaction->amount,
                'Payment Status' => $transaction->status,
                'Razorpay Payment ID' => $transaction->razorpay_payment_id,
                'Razorpay Payout ID' => $transaction->razorpay_payout_id,
                'Processed At' => $transaction->processed_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
