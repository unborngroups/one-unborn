<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentApprovalWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_batch_id',
        'approval_level',
        'status',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the payment batch for this approval
     */
    public function paymentBatch(): BelongsTo
    {
        return $this->belongsTo(PaymentBatch::class);
    }

    /**
     * Get the user who approved this
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if this is accountant level approval
     */
    public function isAccountantLevel(): bool
    {
        return $this->approval_level === 'accountant';
    }

    /**
     * Check if this is finance manager level approval
     */
    public function isFinanceManagerLevel(): bool
    {
        return $this->approval_level === 'finance_manager';
    }

    /**
     * Check if this is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if this is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if this is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Mark as approved
     */
    public function markAsApproved(int $userId, string $remarks = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'remarks' => $remarks,
        ]);
    }

    /**
     * Mark as rejected
     */
    public function markAsRejected(string $remarks): void
    {
        $this->update([
            'status' => 'rejected',
            'remarks' => $remarks,
        ]);
    }

    /**
     * Scope for accountant level approvals
     */
    public function scopeAccountantLevel($query)
    {
        return $query->where('approval_level', 'accountant');
    }

    /**
     * Scope for finance manager level approvals
     */
    public function scopeFinanceManagerLevel($query)
    {
        return $query->where('approval_level', 'finance_manager');
    }

    /**
     * Scope for pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved approvals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
