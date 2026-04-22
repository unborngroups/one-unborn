<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceGstSetting extends Model
{
     protected $table = 'finance_gst_settings';

    protected $fillable = [
        'gst_enabled',
        'gst_number',
        'state_code',
        'cgst_rate',
        'sgst_rate',
        'igst_rate',
        'calculation_type',
    ];
}
