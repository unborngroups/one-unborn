<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use SoftDeletes;
    protected $table = 'purchase_invoices';

    protected $fillable = [
        'company_id',
        'invoice_no',
        'invoice_date',
        'due_date',
        'vendor_id',
        'deliverable_id',
        'vendor_name',
        'vendor_name_raw',
        'vendor_email',
        'vendor_phone',
        'vendor_address',
        'vendor_gstin',
        'gstin',
        'po_invoice_file',
        'sub_total',
        'amount',
        'cgst_total',
        'sgst_total',
        'tax_amount',
        'grand_total',
        'raw_json',
        'status',
        'confidence_score',
        'notes',
        'terms',
        'email_log_id',        // From email webhook
        'arc_amount',          // Extracted amounts
        'otc_amount',
        'static_amount',
        'gst_number',          // Vendor GST from invoice
        'type',                // purchase, sale, etc.
        'total_amount',
        'created_by',
        'updated_by',
        'deleted_by',
        'payment_status',
        'razorpay_payment_id',
        'payment_batch_id',
        'auto_payment_enabled',
        'paid_amount',
        'payment_processed_at',
        'payment_failure_reason',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sub_total' => 'float',
        'amount' => 'float',
        'cgst_total' => 'float',
        'sgst_total' => 'float',
        'tax_amount' => 'float',
        'grand_total' => 'float',
        'confidence_score' => 'float',
        'raw_json' => 'array',
        'auto_payment_enabled' => 'boolean',
        'paid_amount' => 'float',
        'payment_processed_at' => 'datetime',
    ];

    public function setConfidenceScoreAttribute($value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['confidence_score'] = null;
            return;
        }

        $numericValue = (float) $value;
        $this->attributes['confidence_score'] = $numericValue <= 1
            ? round($numericValue * 100, 2)
            : round($numericValue, 2);
    }

    public function getConfidenceScoreAttribute($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $numericValue = (float) $value;
        return $numericValue <= 1 ? round($numericValue * 100, 2) : round($numericValue, 2);
    }

    public function deliverable(): BelongsTo
    {
        return $this->belongsTo(Deliverables::class, 'deliverable_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function emailLog(): BelongsTo
    {
        return $this->belongsTo(EmailLog::class);
    }

    public function paymentBatch(): BelongsTo
    {
        return $this->belongsTo(PaymentBatch::class, 'payment_batch_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'completed' && $this->paid_amount >= $this->grand_total;
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && !$this->isPaid();
    }

    public function getRemainingAmount(): float
    {
        return max(0, $this->grand_total - $this->paid_amount);
    }
}

