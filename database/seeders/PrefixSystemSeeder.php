<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrefixSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Financial Years are now handled by FinancialYearManager automatically
        // We only need to ensure prefix configurations exist
        
        if (\App\Models\PrefixConfiguration::count() == 0) {
            \App\Services\PrefixGenerator::initializeDefaultConfigs();
            $this->command->info('✅ Prefix Configurations created');
        } else {
            $this->command->info('⏭️ Prefix Configurations already exist, skipping');
        }
        
        // Ensure at least one financial year exists (will auto-create current one)
        $activeFY = \App\Models\FinancialYear::getActiveFY();
        $this->command->info('✅ Active Financial Year: ' . $activeFY->name);
        
        $this->command->info('Prefix system ready!');
    }

    // Financial years are now automatically created by FinancialYearManager
    // No need for manual creation method
}
