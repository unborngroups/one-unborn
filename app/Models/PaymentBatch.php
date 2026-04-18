<?php

namespace App\Models;

use App\Models\Company;
use App\Models\PaymentApprovalWorkflow;
use App\Models\PaymentTransaction;
use App\Models\PurchaseInvoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'batch_reference',
        'total_amount',
        'total_invoices',
        'status',
        'approved_by',
        'approved_at',
        'accountant_approved_by',
        'accountant_approved_at',
        'finance_manager_approved_by',
        'finance_manager_approved_at',
        'razorpay_batch_id',
        'processing_notes',
        'failure_reason',
        'processed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'accountant_approved_at' => 'datetime',
        'finance_manager_approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function purchaseInvoices(): HasManyThrough
    {
        return $this->hasManyThrough(
            PurchaseInvoice::class,
            PaymentTransaction::class,
            'payment_batch_id',
            'id',
            'purchase_invoice_id'
        );
    }

    /**
     * Get approval workflows for this batch
     */
    public function approvalWorkflows(): HasMany
    {
        return $this->hasMany(PaymentApprovalWorkflow::class);
    }

    /**
     * Get accountant approval workflow
     */
    public function accountantApproval(): HasOne
    {
        return $this->hasOne(PaymentApprovalWorkflow::class)
            ->where('approval_level', 'accountant');
    }

    /**
     * Get finance manager approval workflow
     */
    public function financeManagerApproval(): HasOne
    {
        return $this->hasOne(PaymentApprovalWorkflow::class)
            ->where('approval_level', 'finance_manager');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function canBeProcessed(): bool
    {
        return in_array($this->status, ['pending_approval', 'pending']);
    }

    public function getSuccessfulTransactionsCount(): int
    {
        return $this->paymentTransactions()->where('status', 'completed')->count();
    }

    public function getFailedTransactionsCount(): int
    {
        return $this->paymentTransactions()->where('status', 'failed')->count();
    }

    public function getProcessedAmount(): float
    {
        return $this->paymentTransactions()->where('status', 'completed')->sum('amount');
    }
}
