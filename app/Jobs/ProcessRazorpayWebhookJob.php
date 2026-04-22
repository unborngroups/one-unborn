<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\RazorpayWebhook;
use App\Services\RazorpayService;
use App\Services\PaymentService;
use App\Models\PaymentTransaction;
use App\Models\PaymentBatch;
use App\Models\PurchaseInvoice;

class ProcessRazorpayWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 300; // 5 minutes

    public function __construct(
        public RazorpayWebhook $webhook
    ) {}

    public function handle(RazorpayService $razorpayService, PaymentService $paymentService): void
    {
        Log::info('Processing Razorpay webhook', [
            'webhook_id' => $this->webhook->id,
            'event_type' => $this->webhook->event_type,
        ]);

        try {
            $payload = $this->webhook->payload;

            switch ($this->webhook->event_type) {
                case 'payout.processed':
                    $this->handlePayoutProcessed($payload, $paymentService);
                    break;

                case 'payout.failed':
                    $this->handlePayoutFailed($payload, $paymentService);
                    break;

                case 'payout.reversed':
                    $this->handlePayoutReversed($payload, $paymentService);
                    break;

                default:
                    Log::info('Unhandled webhook event type', [
                        'event_type' => $this->webhook->event_type,
                    ]);
            }

            // Mark webhook as processed
            $this->webhook->markAsProcessed();

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'webhook_id' => $this->webhook->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->webhook->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    private function handlePayoutProcessed(array $payload, PaymentService $paymentService): void
    {
        $payoutId = $payload['payout']['id'] ?? null;
        $referenceId = $payload['payout']['reference_id'] ?? null;

        if (!$payoutId || !$referenceId) {
            Log::warning('Invalid payout processed webhook payload', ['payload' => $payload]);
            return;
        }

        // Find transaction by reference ID or Razorpay payment ID
        $transaction = PaymentTransaction::where('razorpay_payment_id', $referenceId)
            ->orWhere('razorpay_payment_id', $payoutId)
            ->first();

        if (!$transaction) {
            Log::warning('Transaction not found for payout', [
                'payout_id' => $payoutId,
                'reference_id' => $referenceId,
            ]);
            return;
        }

        // Update transaction
        $transaction->markAsCompleted($payoutId, $payload);

        // Update invoice
        $invoice = $transaction->purchaseInvoice;
        $invoice->update([
            'payment_status' => 'completed',
            'razorpay_payment_id' => $payoutId,
            'paid_amount' => $transaction->amount,
            'payment_processed_at' => now(),
        ]);

        // Check if batch is fully processed
        $batch = $transaction->paymentBatch;
        $this->updateBatchStatus($batch);

        Log::info('Payout processed successfully', [
            'transaction_id' => $transaction->id,
            'payout_id' => $payoutId,
            'invoice_id' => $invoice->id,
        ]);
    }

    private function handlePayoutFailed(array $payload, PaymentService $paymentService): void
    {
        $payoutId = $payload['payout']['id'] ?? null;
        $referenceId = $payload['payout']['reference_id'] ?? null;

        if (!$payoutId || !$referenceId) {
            Log::warning('Invalid payout failed webhook payload', ['payload' => $payload]);
            return;
        }

        $transaction = PaymentTransaction::where('razorpay_payment_id', $referenceId)
            ->orWhere('razorpay_payment_id', $payoutId)
            ->first();

        if (!$transaction) {
            Log::warning('Transaction not found for failed payout', [
                'payout_id' => $payoutId,
                'reference_id' => $referenceId,
            ]);
            return;
        }

        $failureReason = $payload['payout']['failure_reason'] ?? 'Unknown error';

        // Update transaction
        $transaction->markAsFailed($failureReason, $payload);

        // Update invoice
        $invoice = $transaction->purchaseInvoice;
        $invoice->update([
            'payment_status' => 'failed',
            'payment_failure_reason' => $failureReason,
        ]);

        // Check if batch has failures
        $batch = $transaction->paymentBatch;
        $this->updateBatchStatus($batch);

        Log::error('Payout failed', [
            'transaction_id' => $transaction->id,
            'payout_id' => $payoutId,
            'failure_reason' => $failureReason,
        ]);
    }

    private function handlePayoutReversed(array $payload, PaymentService $paymentService): void
    {
        $payoutId = $payload['payout']['id'] ?? null;

        if (!$payoutId) {
            Log::warning('Invalid payout reversed webhook payload', ['payload' => $payload]);
            return;
        }

        $transaction = PaymentTransaction::where('razorpay_payout_id', $payoutId)
            ->orWhere('razorpay_payment_id', $payoutId)
            ->first();

        if (!$transaction) {
            Log::warning('Transaction not found for reversed payout', [
                'payout_id' => $payoutId,
            ]);
            return;
        }

        // Update transaction
        $transaction->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);

        // Update invoice
        $invoice = $transaction->purchaseInvoice;
        $invoice->update([
            'payment_status' => 'refunded',
        ]);

        Log::warning('Payout reversed', [
            'transaction_id' => $transaction->id,
            'payout_id' => $payoutId,
        ]);
    }

    private function updateBatchStatus(PaymentBatch $batch): void
    {
        $transactions = $batch->paymentTransactions;
        $completedCount = $transactions->where('status', 'completed')->count();
        $failedCount = $transactions->where('status', 'failed')->count();
        $totalCount = $transactions->count();

        if ($completedCount + $failedCount === $totalCount) {
            $status = $failedCount === 0 ? 'completed' : 'completed_with_failures';
            
            $batch->update([
                'status' => $status,
                'processed_at' => now(),
                'processing_notes' => "Processed via webhook: {$completedCount} successful, {$failedCount} failed",
            ]);

            Log::info('Batch status updated via webhook', [
                'batch_id' => $batch->id,
                'status' => $status,
                'completed' => $completedCount,
                'failed' => $failedCount,
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Webhook job failed permanently', [
            'webhook_id' => $this->webhook->id,
            'error' => $exception->getMessage(),
        ]);

        $this->webhook->markAsFailed($exception->getMessage());
    }
}
