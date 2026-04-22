<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FinancialYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'current_year',
        'year_format'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Get the currently active financial year (auto-create if missing)
     */
    public static function getActiveFY()
    {
        $activeFY = self::where('is_active', true)->first();
        
        // If no active FY or active FY is outdated, create/activate current one
        if (!$activeFY || !$activeFY->isDateInRange(now())) {
            return \App\Services\FinancialYearManager::activateCurrentFY();
        }
        
        return $activeFY;
    }

    /**
     * Get financial year by date
     */
    public static function getByDate($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        return self::where('start_date', '<=', $date)
                   ->where('end_date', '>=', $date)
                   ->first();
    }

    /**
     * Get formatted year string
     */
    public function getFormattedYear()
    {
        switch ($this->year_format) {
            case 'YY-YY':
                return substr($this->current_year, -2) . '-' . substr($this->current_year + 1, -2);
            case 'YYYY-YY':
                return $this->current_year . '-' . substr($this->current_year + 1, -2);
            case 'YYYY':
                return (string) $this->current_year;
            default:
                return $this->name;
        }
    }

    /**
     * Check if given date is in this financial year
     */
    public function isDateInRange($date)
    {
        $date = Carbon::parse($date);
        return $date->between($this->start_date, $this->end_date);
    }
}
