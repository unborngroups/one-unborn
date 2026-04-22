<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyPaymentSettings;
use App\Models\Company;

class CompanyPaymentSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            // Check if settings already exist
            $existingSettings = CompanyPaymentSettings::where('company_id', $company->id)->first();
            
            if (!$existingSettings) {
                CompanyPaymentSettings::create([
                    'company_id' => $company->id,
                    'razorpay_key_id' => 'rzp_test_' . $company->id . '_demo',
                    'razorpay_key_secret' => 'demo_secret_' . $company->id,
                    'razorpay_account_number' => 'acc_' . $company->id . '_demo',
                    'webhook_secret' => 'webhook_secret_' . $company->id,
                    'payment_batching_mode' => 'single',
                    'cashflow_control_enabled' => false,
                    'minimum_balance_threshold' => 1000.00,
                    'max_daily_payout_limit' => 100000.00,
                    'isolation_enabled' => true,
                    'approval_workflow' => 'two_level',
                ]);
                
                echo "Payment settings created for company {$company->id}\n";
            } else {
                echo "Payment settings already exist for company {$company->id}\n";
            }
        }
    }
}
