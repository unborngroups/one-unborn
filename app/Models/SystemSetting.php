<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'timezone', 
        'date_format', 
        'language', 
        'currency_symbol', 
        'fiscal_start_month',
        'surepass_api_token',
        'surepass_api_environment',
        'whatsapp_default_number',
        'whatsapp_enabled'
    ];
}
