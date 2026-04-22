<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppSettings extends Model
{
    protected $table = 'whats_app_settings';
    
    protected $fillable = [
        // WhatsApp API Settings
        'official_phone',
        'official_account_id',
        'official_access_token',
        'official_phone_id',
        'official_enabled',
        'unofficial_api_url',
        'unofficial_mobile',
        'unofficial_access_token',
        'unofficial_instance_id',
        'unofficial_enabled',
        'is_default',
    ];
    
    protected $casts = [
        'official_enabled' => 'boolean',
        'unofficial_enabled' => 'boolean',
        'is_default' => 'boolean',
    ];
}
