<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentBatch;
use App\Services\PaymentService;
use App\Services\RazorpayService;

class ProcessPaymentBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 600; // 10 minutes

    public function __construct(
        public PaymentBatch $paymentBatch,
        public int $companyId
    ) {}

    public function handle(PaymentService $paymentService): void
    {
        Log::info('Starting payment batch processing', [
            'batch_id' => $this->paymentBatch->id,
            'batch_reference' => $this->paymentBatch->batch_reference,
        ]);

        try {
            // Initialize Razorpay service for this company
            $razorpayService = new RazorpayService($this->companyId);
            $paymentService = new PaymentService($razorpayService);

            // Process the payment batch
            $result = $paymentService->processPaymentBatch($this->paymentBatch);

            Log::info('Payment batch processed successfully', [
                'batch_id' => $this->paymentBatch->id,
                'result' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment batch processing failed', [
                'batch_id' => $this->paymentBatch->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mark batch as failed
            $this->paymentBatch->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Payment batch job failed permanently', [
            'batch_id' => $this->paymentBatch->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Mark batch as failed
        $this->paymentBatch->update([
            'status' => 'failed',
            'failure_reason' => 'Job failed after ' . $this->attempts() . ' attempts: ' . $exception->getMessage(),
            'processed_at' => now(),
        ]);
    }
}
