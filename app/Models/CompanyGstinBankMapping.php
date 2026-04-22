<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyGstinBankMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'gstin',
        'bank_account_number',
        'bank_account_holder',
        'bank_ifsc',
        'bank_name',
        'is_primary',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company for this mapping
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get primary bank account for GSTIN
     */
    public static function getPrimaryForCompanyAndGstin(int $companyId, string $gstin): ?self
    {
        return static::where([
            'company_id' => $companyId,
            'gstin' => $gstin,
            'is_primary' => true,
            'is_active' => true,
        ])->first();
    }

    /**
     * Get all active bank accounts for GSTIN
     */
    public static function getActiveForCompanyAndGstin(int $companyId, string $gstin): \Illuminate\Database\Eloquent\Collection
    {
        return static::where([
            'company_id' => $companyId,
            'gstin' => $gstin,
            'is_active' => true,
        ])->get();
    }

    /**
     * Get bank account for payout
     */
    public static function getForPayout(int $companyId, string $gstin): ?self
    {
        return static::where([
            'company_id' => $companyId,
            'gstin' => $gstin,
            'is_active' => true,
        ])->orderBy('is_primary', 'desc')->first();
    }
}
