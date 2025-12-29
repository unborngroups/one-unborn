<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTdsSetting extends Model
{
    protected $table = 'finance_tds_settings';

    protected $fillable = [
        'tds_enabled',
        'section',
        'tds_rate',
        'threshold_amount',
        'deduction_on',
    ];
}
