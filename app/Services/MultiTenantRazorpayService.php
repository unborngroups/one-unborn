<?php

namespace App\Services;

use App\Models\CompanyPaymentSettings;
use App\Models\CompanyGstinBankMapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MultiTenantRazorpayService
{
    /**
     * Get Razorpay credentials for specific company
     */
    public function getCredentialsForCompany(int $companyId): array
    {
        $cacheKey = "razorpay_credentials_{$companyId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($companyId) {
            $settings = CompanyPaymentSettings::getForCompany($companyId);
            
            if (!$settings) {
                // Create default settings if they don't exist
                $settings = CompanyPaymentSettings::createDefaultsForCompany($companyId);
            }

            return [
                'key_id' => $settings->razorpay_key_id,
                'key_secret' => $settings->razorpay_key_secret,
                'account_number' => $settings->razorpay_account_number,
                'webhook_secret' => $settings->webhook_secret,
            ];
        });
    }

    /**
     * Get bank account for payout based on GSTIN
     */
    public function getBankAccountForPayout(int $companyId, string $gstin): ?array
    {
        // Get primary bank account for this GSTIN
        $mapping = CompanyGstinBankMapping::getPrimaryForCompanyAndGstin($companyId, $gstin);
        
        if (!$mapping) {
            Log::warning("No bank account mapping found for GSTIN: {$gstin}", [
                'company_id' => $companyId,
                'gstin' => $gstin,
            ]);
            return null;
        }

        return [
            'account_number' => $mapping->bank_account_number,
            'account_holder' => $mapping->bank_account_holder,
            'ifsc_code' => $mapping->bank_ifsc,
            'bank_name' => $mapping->bank_name,
        ];
    }

    /**
     * Validate payout before execution
     */
    public function validatePayout(int $companyId, float $amount): array
    {
        $settings = CompanyPaymentSettings::getForCompany($companyId);
        
        if (!$settings) {
            return [
                'valid' => false,
                'error' => 'Company payment settings not configured',
            ];
        }

        // Check cashflow control
        if ($settings->isCashflowControlEnabled()) {
            // Check Razorpay balance
            $balance = $this->getRazorpayBalance($companyId);
            $threshold = $settings->minimum_balance_threshold;
            
            if ($balance < $threshold) {
                return [
                    'valid' => false,
                    'error' => "Insufficient balance. Current: ₹{$balance}, Required: ₹{$threshold}",
                ];
            }

            // Check daily limit
            $dailyLimit = $settings->max_daily_payout_limit;
            $todayPayouts = $this->getTodayPayoutTotal($companyId);
            
            if (($todayPayouts + $amount) > $dailyLimit) {
                return [
                    'valid' => false,
                    'error' => "Daily payout limit exceeded. Today: ₹{$todayPayouts}, Limit: ₹{$dailyLimit}, Attempted: ₹{$amount}",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Get Razorpay account balance
     */
    public function getRazorpayBalance(int $companyId): float
    {
        try {
            $credentials = $this->getCredentialsForCompany($companyId);
            $razorpayService = new RazorpayService($companyId);
            
            $response = $razorpayService->getBalance();
            
            return $response['balance'] ?? 0;
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch Razorpay balance', [
                'company_id' => $companyId,
                'error' => $e->getMessage(),
            ]);
            
            return 0;
        }
    }

    /**
     * Get today's total payouts
     */
    public function getTodayPayoutTotal(int $companyId): float
    {
        return \App\Models\PaymentTransaction::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->whereDate('created_at', now()->toDateString())
        ->sum('amount');
    }

    /**
     * Check if company is isolated
     */
    public function isCompanyIsolated(int $companyId): bool
    {
        $settings = CompanyPaymentSettings::getForCompany($companyId);
        return $settings ? $settings->isolation_enabled : true;
    }

    /**
     * Get company approval workflow
     */
    public function getApprovalWorkflow(int $companyId): string
    {
        $settings = CompanyPaymentSettings::getForCompany($companyId);
        return $settings ? $settings->approval_workflow : 'two_level';
    }

    /**
     * Get payment batching mode
     */
    public function getPaymentBatchingMode(int $companyId): string
    {
        $settings = CompanyPaymentSettings::getForCompany($companyId);
        return $settings ? $settings->payment_batching_mode : 'single';
    }
}
