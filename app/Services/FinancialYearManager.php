<?php

namespace App\Services;

use App\Models\FinancialYear;
use Carbon\Carbon;

class FinancialYearManager
{
    /**
     * Get or create financial year for a given date
     */
    public static function getOrCreateFYForDate($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        // Try to find existing FY for this date
        $existingFY = FinancialYear::where('start_date', '<=', $date)
                                  ->where('end_date', '>=', $date)
                                  ->first();
        
        if ($existingFY) {
            return $existingFY;
        }
        
        // Create new FY if not exists
        return self::createFYForDate($date);
    }
    
    /**
     * Create financial year for a given date
     */
    private static function createFYForDate($date)
    {
        // Determine FY start based on date
        $startYear = $date->month >= 4 ? $date->year : $date->year - 1;
        $endYear = $startYear + 1;
        $startDate = Carbon::createFromDate($startYear, 4, 1);
        $endDate = Carbon::createFromDate($endYear, 3, 31);
        $fyName = $startYear . '-' . substr($endYear, -2);

        // Prevent duplicate FY creation
        $existingFY = FinancialYear::where('name', $fyName)->first();
        if ($existingFY) {
            return $existingFY;
        }

        return FinancialYear::create([
            'name' => $fyName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => false, // Will be activated separately
            'current_year' => $startYear,
            'year_format' => 'YY-YY'
        ]);
        
        $startDate = Carbon::createFromDate($startYear, 4, 1);
        $endDate = Carbon::createFromDate($endYear, 3, 31);
        
        $fyName = $startYear . '-' . substr($endYear, -2);
        
        return FinancialYear::create([
            'name' => $fyName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => false, // Will be activated separately
            'current_year' => $startYear,
            'year_format' => 'YY-YY'
        ]);
    }
    
    /**
     * Automatically activate current financial year
     */
    public static function activateCurrentFY()
    {
        $currentDate = Carbon::now();
        
        // Deactivate all FYs first
        FinancialYear::where('is_active', true)->update(['is_active' => false]);
        
        // Get or create current FY
        $currentFY = self::getOrCreateFYForDate($currentDate);
        
        // Activate it
        $currentFY->update(['is_active' => true]);
        
        return $currentFY;
    }
    
    /**
     * Create financial years for a range (for bulk creation)
     */
    public static function createFYRange($startYear, $endYear)
    {
        $createdFYs = [];
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $startDate = Carbon::createFromDate($year, 4, 1);
            $currentFY = self::getOrCreateFYForDate($startDate);
            $createdFYs[] = $currentFY;
        }
        
        return $createdFYs;
    }
    
    /**
     * Yearly maintenance - run this every April 1st
     */
    public static function yearlyMaintenance()
    {
        // Activate current FY
        $currentFY = self::activateCurrentFY();
        
        // Create next 3 years in advance
        $nextYear = Carbon::now()->addYears(3)->year;
        self::createFYRange(Carbon::now()->year, $nextYear);
        
        // Reset sequences for yearly configs
        self::resetYearlySequences();
        
        return $currentFY;
    }
    
    /**
     * Reset sequences for configurations that have yearly reset
     */
    private static function resetYearlySequences()
    {
        \App\Models\PrefixConfiguration::where('reset_yearly', true)
                                       ->update(['current_sequence' => 0]);
    }
}