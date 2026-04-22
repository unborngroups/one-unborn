<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\PaymentBatch;
use App\Models\PurchaseInvoice;
use App\Models\RazorpayWebhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentReconciliationService
{
    /**
     * Reconcile payments with Razorpay
     */
    public function reconcilePayments(int $companyId, string $period = 'month'): array
    {
        $startDate = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $endDate = match ($period) {
            'today' => now()->endOfDay(),
            'week' => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'quarter' => now()->endOfQuarter(),
            'year' => now()->endOfYear(),
            default => now()->endOfMonth(),
        };

        return [
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'reconciliation_data' => $this->performReconciliation($companyId, $startDate, $endDate),
        ];
    }

    /**
     * Perform actual reconciliation
     */
    private function performReconciliation(int $companyId, $startDate, $endDate): array
    {
        // Get all transactions in period
        $transactions = PaymentTransaction::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->with(['paymentBatch', 'purchaseInvoice'])
        ->get();

        // Get webhook events for same period
        $webhooks = RazorpayWebhook::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();

        $reconciliation = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->sum('amount'),
            'successful_transactions' => $transactions->where('status', 'completed')->count(),
            'successful_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'failed_amount' => $transactions->where('status', 'failed')->sum('amount'),
            'pending_transactions' => $transactions->where('status', 'processing')->count(),
            'pending_amount' => $transactions->where('status', 'processing')->sum('amount'),
            'webhook_events' => $webhooks->count(),
            'discrepancies' => [],
        ];

        // Find discrepancies
        foreach ($transactions as $transaction) {
            $discrepancy = $this->findDiscrepancy($transaction, $webhooks);
            if ($discrepancy) {
                $reconciliation['discrepancies'][] = $discrepancy;
            }
        }

        // Calculate success rate
        $reconciliation['success_rate'] = $reconciliation['total_transactions'] > 0 
            ? round(($reconciliation['successful_transactions'] / $reconciliation['total_transactions']) * 100, 2) 
            : 0;

        return $reconciliation;
    }

    /**
     * Find discrepancy for a transaction
     */
    private function findDiscrepancy(PaymentTransaction $transaction, $webhooks): ?array
    {
        $matchingWebhook = $webhooks->firstWhere('razorpay_payment_id', $transaction->razorpay_payment_id);

        if (!$matchingWebhook) {
            return [
                'type' => 'missing_webhook',
                'transaction_id' => $transaction->id,
                'razorpay_payment_id' => $transaction->razorpay_payment_id,
                'amount' => $transaction->amount,
                'description' => 'No webhook event found for this payout',
            ];
        }

        // Check if webhook status matches transaction status
        if ($matchingWebhook->event_type === 'payout.processed') {
            if ($transaction->status !== 'completed') {
                return [
                    'type' => 'status_mismatch',
                    'transaction_id' => $transaction->id,
                    'razorpay_payment_id' => $transaction->razorpay_payment_id,
                    'local_status' => $transaction->status,
                    'webhook_status' => 'completed',
                    'description' => 'Local status not updated to completed after webhook',
                ];
            }
        }

        if ($matchingWebhook->event_type === 'payout.failed') {
            if ($transaction->status !== 'failed') {
                return [
                    'type' => 'status_mismatch',
                    'transaction_id' => $transaction->id,
                    'razorpay_payment_id' => $transaction->razorpay_payment_id,
                    'local_status' => $transaction->status,
                    'webhook_status' => 'failed',
                    'description' => 'Local status not updated to failed after webhook',
                ];
            }
        }

        return null;
    }

    /**
     * Auto-fix common discrepancies
     */
    public function autoFixDiscrepancies(array $discrepancies): array
    {
        $fixed = [];
        $failed = [];

        foreach ($discrepancies as $discrepancy) {
            try {
                switch ($discrepancy['type']) {
                    case 'missing_webhook':
                        // Can't auto-fix missing webhook
                        $failed[] = $discrepancy;
                        break;

                    case 'status_mismatch':
                        // Auto-fix status mismatch
                        $transaction = PaymentTransaction::find($discrepancy['transaction_id']);
                        if ($transaction) {
                            $transaction->update([
                                'status' => $discrepancy['webhook_status'],
                                'updated_at' => now(),
                            ]);
                            $fixed[] = [
                                'discrepancy' => $discrepancy,
                                'action_taken' => 'Updated transaction status to match webhook',
                            ];
                        }
                        break;

                    default:
                        $failed[] = $discrepancy;
                        break;
                }
            } catch (\Exception $e) {
                Log::error('Failed to auto-fix discrepancy', [
                    'discrepancy' => $discrepancy,
                    'error' => $e->getMessage(),
                ]);
                $failed[] = $discrepancy;
            }
        }

        return [
            'total_discrepancies' => count($discrepancies),
            'fixed_count' => count($fixed),
            'failed_count' => count($failed),
            'fixed' => $fixed,
            'failed' => $failed,
        ];
    }

    /**
     * Generate reconciliation report
     */
    public function generateReconciliationReport(int $companyId, string $period = 'month'): array
    {
        $reconciliation = $this->reconcilePayments($companyId, $period);
        
        $report = [
            'company_id' => $companyId,
            'generated_at' => now()->toDateTimeString(),
            'period' => $reconciliation['period'],
            'summary' => [
                'total_transactions' => $reconciliation['reconciliation_data']['total_transactions'],
                'total_amount' => $reconciliation['reconciliation_data']['total_amount'],
                'success_rate' => $reconciliation['reconciliation_data']['success_rate'],
                'successful_transactions' => $reconciliation['reconciliation_data']['successful_transactions'],
                'successful_amount' => $reconciliation['reconciliation_data']['successful_amount'],
                'failed_transactions' => $reconciliation['reconciliation_data']['failed_transactions'],
                'failed_amount' => $reconciliation['reconciliation_data']['failed_amount'],
                'discrepancies_found' => count($reconciliation['reconciliation_data']['discrepancies']),
            ],
            'discrepancies' => $reconciliation['reconciliation_data']['discrepancies'],
        ];

        Log::info('Reconciliation report generated', [
            'company_id' => $companyId,
            'period' => $period,
            'discrepancies_count' => count($reconciliation['reconciliation_data']['discrepancies']),
        ]);

        return $report;
    }

    /**
     * Get reconciliation statistics
     */
    public function getReconciliationStats(int $companyId, string $period = 'month'): array
    {
        $reconciliation = $this->reconcilePayments($companyId, $period);
        $data = $reconciliation['reconciliation_data'];

        return [
            'health_score' => $this->calculateHealthScore($data),
            'metrics' => [
                'total_transactions' => $data['total_transactions'],
                'success_rate' => $data['success_rate'],
                'total_amount' => $data['total_amount'],
                'discrepancy_rate' => $data['total_transactions'] > 0 
                    ? round((count($data['discrepancies']) / $data['total_transactions']) * 100, 2) 
                    : 0,
            ],
            'trends' => $this->calculateTrends($companyId, $period),
        ];
    }

    /**
     * Calculate health score
     */
    private function calculateHealthScore(array $data): int
    {
        $score = 100;

        // Deduct points for failures
        if ($data['total_transactions'] > 0) {
            $failureRate = ($data['failed_transactions'] / $data['total_transactions']) * 100;
            $score -= $failureRate * 2; // 2 points per % failure
        }

        // Deduct points for discrepancies
        $discrepancyRate = ($data['total_transactions'] > 0) 
            ? (count($data['discrepancies']) / $data['total_transactions']) * 100 
            : 0;
        $score -= $discrepancyRate * 5; // 5 points per % discrepancy

        return max(0, min(100, (int) $score));
    }

    /**
     * Calculate trends
     */
    private function calculateTrends(int $companyId, string $period): array
    {
        // Compare with previous period
        $previousStart = match ($period) {
            'month' => now()->subMonth()->startOfMonth(),
            'quarter' => now()->subQuarter()->startOfQuarter(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subMonth()->startOfMonth(),
        };

        $previousEnd = match ($period) {
            'month' => now()->subMonth()->endOfMonth(),
            'quarter' => now()->subQuarter()->endOfQuarter(),
            'year' => now()->subYear()->endOfYear(),
            default => now()->subMonth()->endOfMonth(),
        };

        $currentTransactions = PaymentTransaction::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->count();

        $previousTransactions = PaymentTransaction::whereHas('paymentBatch', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })
        ->whereBetween('created_at', [$previousStart, $previousEnd])
        ->count();

        $trend = $previousTransactions > 0 
            ? (($currentTransactions - $previousTransactions) / $previousTransactions) * 100 
            : 0;

        return [
            'current_period_transactions' => $currentTransactions,
            'previous_period_transactions' => $previousTransactions,
            'trend_percentage' => round($trend, 2),
            'trend_direction' => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable'),
        ];
    }
}
