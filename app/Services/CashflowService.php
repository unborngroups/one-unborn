<?php

namespace App\Services;

use App\Models\CompanyPaymentSettings;
use App\Models\PaymentTransaction;
use App\Models\PaymentBatch;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CashflowService
{
    /**
     * Check if payout should be blocked due to cashflow controls
     */
    public function validatePayout(int $companyId, float $amount): array
    {
        $settings = CompanyPaymentSettings::getForCompany($companyId);
        
        if (!$settings) {
            return [
                'allowed' => true,
                'reason' => null,
            ];
        }

        // Check cashflow control
        if (!$settings->isCashflowControlEnabled()) {
            return [
                'allowed' => true,
                'reason' => null,
            ];
        }

        // Check minimum balance threshold
        $multiTenantService = new MultiTenantRazorpayService();
        $currentBalance = $multiTenantService->getRazorpayBalance($companyId);
        $minimumThreshold = $settings->minimum_balance_threshold;

        if ($currentBalance < $minimumThreshold) {
            return [
                'allowed' => false,
                'reason' => "Balance below minimum threshold. Current: ₹{$currentBalance}, Required: ₹{$minimumThreshold}",
            ];
        }

        // Check daily limit
        $dailyLimit = $settings->max_daily_payout_limit;
        $todayPayouts = $multiTenantService->getTodayPayoutTotal($companyId);

        if (($todayPayouts + $amount) > $dailyLimit) {
            return [
                'allowed' => false,
                'reason' => "Daily payout limit exceeded. Today: ₹{$todayPayouts}, Limit: ₹{$dailyLimit}, Attempted: ₹{$amount}",
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
        ];
    }

    /**
     * Get cashflow status for company
     */
    public function getCashflowStatus(int $companyId): array
    {
        $settings = CompanyPaymentSettings::getForCompany($companyId);
        $multiTenantService = new MultiTenantRazorpayService();
        
        $currentBalance = $multiTenantService->getRazorpayBalance($companyId);
        $dailyLimit = $settings->max_daily_payout_limit ?? 100000.00;
        $todayPayouts = $multiTenantService->getTodayPayoutTotal($companyId);
        $monthlyPayouts = $this->getMonthlyPayoutTotal($companyId);

        return [
            'cashflow_control_enabled' => $settings->isCashflowControlEnabled(),
            'current_balance' => $currentBalance,
            'daily_limit' => $dailyLimit,
            'today_payouts' => $todayPayouts,
            'remaining_daily_limit' => max(0, $dailyLimit - $todayPayouts),
            'monthly_payouts' => $monthlyPayouts,
            'utilization_percentage' => $dailyLimit > 0 ? round(($todayPayouts / $dailyLimit) * 100, 2) : 0,
        ];
    }

    /**
     * Get monthly payout total
     */
    private function getMonthlyPayoutTotal(int $companyId): float
    {
        return PaymentTransaction::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');
    }

    /**
     * Check for duplicate payout attempts
     */
    public function checkDuplicatePayout(int $companyId, array $payoutData): bool
    {
        $referenceId = $payoutData['reference_id'] ?? null;
        
        if (!$referenceId) {
            return false;
        }

        // Check if payout with same reference already exists
        return PaymentTransaction::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('razorpay_payment_id', $referenceId)
        ->whereIn('status', ['processing', 'completed'])
        ->exists();
    }

    /**
     * Get payout recommendations based on cashflow
     */
    public function getPayoutRecommendations(int $companyId): array
    {
        $settings = CompanyPaymentSettings::getForCompany($companyId);
        $multiTenantService = new MultiTenantRazorpayService();
        $currentBalance = $multiTenantService->getRazorpayBalance($companyId);
        $dailyLimit = $settings->max_daily_payout_limit ?? 100000.00;
        $todayPayouts = $multiTenantService->getTodayPayoutTotal($companyId);

        $recommendations = [];

        // Low balance warning
        if ($currentBalance < 5000) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'Low balance detected. Consider topping up Razorpay account.',
                'priority' => 'high',
                'action' => 'topup_account',
            ];
        }

        // High utilization warning
        $utilization = $dailyLimit > 0 ? ($todayPayouts / $dailyLimit) * 100 : 0;
        if ($utilization > 80) {
            $recommendations[] = [
                'type' => 'warning',
                'message' => "High daily limit utilization ({$utilization}%). Consider increasing daily limit.",
                'priority' => 'medium',
                'action' => 'review_limit',
            ];
        }

        // Approaching limit
        $remainingLimit = $dailyLimit - $todayPayouts;
        if ($remainingLimit < ($dailyLimit * 0.2)) { // Less than 20% remaining
            $recommendations[] = [
                'type' => 'info',
                'message' => "Approaching daily limit. Only ₹{$remainingLimit} remaining today.",
                'priority' => 'low',
                'action' => 'monitor_closely',
            ];
        }

        return $recommendations;
    }

    /**
     * Generate cashflow report
     */
    public function generateCashflowReport(int $companyId, string $period = 'month'): array
    {
        $multiTenantService = new MultiTenantRazorpayService();
        
        $startDate = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $transactions = PaymentTransaction::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->where('created_at', '>=', $startDate)
        ->orderBy('created_at', 'desc')
        ->get(['id', 'amount', 'razorpay_payment_id', 'status', 'created_at']);

        $report = [
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->sum('amount'),
            'successful_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'failed_amount' => $transactions->where('status', 'failed')->sum('amount'),
            'success_rate' => $transactions->count() > 0 ? round(($transactions->where('status', 'completed')->sum('amount') / $transactions->sum('amount')) * 100, 2) : 0,
        'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'razorpay_payment_id' => $transaction->razorpay_payment_id,
                    'date' => $transaction->created_at->toDateString(),
                ];
            })->toArray(),
        ];

        Log::info('Cashflow report generated', [
            'company_id' => $companyId,
            'period' => $period,
            'total_amount' => $report['total_amount'],
        ]);

        return $report;
    }
}
