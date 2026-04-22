<?php

namespace App\Console\Commands;

use App\Services\PaymentReconciliationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PaymentsReconcileCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payments:reconcile {--company=} {--period=}';

    /**
     * The console command description.
     */
    protected $description = 'Reconcile payments with Razorpay and generate reports';

    /**
     * Execute the console command.
     */
    public function handle(PaymentReconciliationService $reconciliationService): int
    {
        $companyId = $this->option('company');
        $period = $this->option('period') ?? 'month';

        if (!$companyId) {
            $this->error('Company ID is required. Use --company=<id>');
            return 1;
        }

        $this->info("Starting payment reconciliation for company {$companyId} (period: {$period})");

        try {
            // Generate reconciliation report
            $report = $reconciliationService->generateReconciliationReport($companyId, $period);
            
            // Display summary
            $this->displaySummary($report);
            
            // Check for discrepancies
            $discrepancies = $report['discrepancies'];
            if (!empty($discrepancies)) {
                $this->warn("Found " . count($discrepancies) . " discrepancies");
                
                // Attempt auto-fix
                $fixResult = $reconciliationService->autoFixDiscrepancies($discrepancies);
                
                if ($fixResult['fixed_count'] > 0) {
                    $this->info("Auto-fixed {$fixResult['fixed_count']} discrepancies");
                }
                
                if ($fixResult['failed_count'] > 0) {
                    $this->error("Failed to fix {$fixResult['failed_count']} discrepancies");
                }
            } else {
                $this->info("No discrepancies found - all transactions reconciled successfully");
            }

            // Calculate health score
            $stats = $reconciliationService->getReconciliationStats($companyId, $period);
            $this->displayHealthScore($stats);

            $this->info("Reconciliation completed successfully");
            return 0;

        } catch (\Exception $e) {
            $this->error("Reconciliation failed: " . $e->getMessage());
            Log::error('Payment reconciliation command failed', [
                'company_id' => $companyId,
                'period' => $period,
                'error' => $e->getMessage(),
            ]);
            return 1;
        }
    }

    /**
     * Display reconciliation summary
     */
    private function displaySummary(array $report): void
    {
        $this->info("\n=== RECONCILIATION SUMMARY ===");
        $this->info("Period: {$report['period']} ({$report['start_date']} to {$report['end_date']})");
        $this->info("Total Transactions: {$report['summary']['total_transactions']}");
        $this->info("Total Amount: ₹" . number_format($report['summary']['total_amount'], 2));
        $this->info("Successful Transactions: {$report['summary']['successful_transactions']}");
        $this->info("Successful Amount: ₹" . number_format($report['summary']['successful_amount'], 2));
        $this->info("Failed Transactions: {$report['summary']['failed_transactions']}");
        $this->info("Failed Amount: ₹" . number_format($report['summary']['failed_amount'], 2));
        $this->info("Success Rate: {$report['summary']['success_rate']}%");
    }

    /**
     * Display health score
     */
    private function displayHealthScore(array $stats): void
    {
        $healthScore = $stats['health_score'];
        $metrics = $stats['metrics'];
        
        $this->info("\n=== HEALTH SCORE ===");
        $this->info("Overall Score: {$healthScore}/100");
        
        if ($healthScore >= 90) {
            $this->info("Status: EXCELLENT");
        } elseif ($healthScore >= 80) {
            $this->info("Status: GOOD");
        } elseif ($healthScore >= 70) {
            $this->warn("Status: FAIR");
        } else {
            $this->error("Status: POOR");
        }
        
        $this->info("Success Rate: {$metrics['success_rate']}%");
        $this->info("Discrepancy Rate: {$metrics['discrepancy_rate']}%");
    }
}
