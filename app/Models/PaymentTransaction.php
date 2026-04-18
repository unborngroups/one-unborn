<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'payment_batch_id',
        'purchase_invoice_id',
        'razorpay_payment_id',
        'razorpay_payout_id',
        'amount',
        'status',
        'razorpay_response',
        'failure_reason',
        'processed_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'razorpay_response' => 'array',
        'processed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function paymentBatch(): BelongsTo
    {
        return $this->belongsTo(PaymentBatch::class);
    }

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function markAsCompleted(string $payoutId, array $response = []): void
    {
        $this->update([
            'status' => 'completed',
            'razorpay_payout_id' => $payoutId,
            'razorpay_response' => $response,
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason, array $response = []): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'razorpay_response' => $response,
            'processed_at' => now(),
        ]);
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
        ]);
    }
}
