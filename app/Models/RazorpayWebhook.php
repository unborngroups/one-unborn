<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RazorpayWebhook extends Model
{
    protected $fillable = [
        'webhook_id',
        'event_type',
        'company_id',
        'payload',
        'processed',
        'processing_error',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function markAsProcessed(): void
    {
        $this->update([
            'processed' => true,
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'processed' => false,
            'processing_error' => $error,
            'processed_at' => now(),
        ]);
    }

    public function isPayoutEvent(): bool
    {
        return in_array($this->event_type, [
            'payout.processed',
            'payout.failed',
            'payout.reversed',
            'payout.updated',
        ]);
    }

    public function isPaymentEvent(): bool
    {
        return in_array($this->event_type, [
            'payment.captured',
            'payment.failed',
            'payment.refunded',
        ]);
    }
}
