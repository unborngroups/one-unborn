<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class CompanyPaymentSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'razorpay_key_id',
        'razorpay_key_secret',
        'razorpay_account_number',
        'webhook_secret',
        'payment_batching_mode',
        'cashflow_control_enabled',
        'minimum_balance_threshold',
        'max_daily_payout_limit',
        'isolation_enabled',
        'approval_workflow',
    ];

    protected $casts = [
        'cashflow_control_enabled' => 'boolean',
        'isolation_enabled' => 'boolean',
        'minimum_balance_threshold' => 'decimal:2',
        'max_daily_payout_limit' => 'decimal:2',
    ];

    /**
     * Get the company for these settings
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get decrypted Razorpay key ID
     */
    public function getRazorpayKeyIdAttribute($value): string
    {
        return $value ? Crypt::decryptString($value) : '';
    }

    /**
     * Get decrypted Razorpay key secret
     */
    public function getRazorpayKeySecretAttribute($value): string
    {
        return $value ? Crypt::decryptString($value) : '';
    }

    /**
     * Get decrypted webhook secret
     */
    public function getWebhookSecretAttribute($value): string
    {
        return $value ? Crypt::decryptString($value) : '';
    }

    /**
     * Set encrypted Razorpay key ID
     */
    public function setRazorpayKeyIdAttribute($value): void
    {
        $this->attributes['razorpay_key_id'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted Razorpay key secret
     */
    public function setRazorpayKeySecretAttribute($value): void
    {
        $this->attributes['razorpay_key_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Set encrypted webhook secret
     */
    public function setWebhookSecretAttribute($value): void
    {
        $this->attributes['webhook_secret'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Check if single level approval
     */
    public function isSingleLevelApproval(): bool
    {
        return $this->approval_workflow === 'single_level';
    }

    /**
     * Check if two level approval
     */
    public function isTwoLevelApproval(): bool
    {
        return $this->approval_workflow === 'two_level';
    }

    /**
     * Check if cashflow control is enabled
     */
    public function isCashflowControlEnabled(): bool
    {
        return $this->cashflow_control_enabled;
    }

    /**
     * Get settings for company
     */
    public static function getForCompany(int $companyId): ?self
    {
        return static::where('company_id', $companyId)->first();
    }

    /**
     * Create default settings for company
     */
    public static function createDefaultsForCompany(int $companyId): self
    {
        return static::create([
            'company_id' => $companyId,
            'payment_batching_mode' => 'single',
            'cashflow_control_enabled' => false,
            'minimum_balance_threshold' => 1000.00,
            'max_daily_payout_limit' => 100000.00,
            'isolation_enabled' => true,
            'approval_workflow' => 'two_level',
        ]);
    }
}
