<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
   use HasFactory, Notifiable   ;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($client) {
            if (empty($client->client_code)) {
                $client->client_code = \App\Services\PrefixGenerator::generateClientCode();
            }
        });
    }

    protected $fillable = [
        'user_name',
        'client_code',
        'pan_number',
        'client_name',
        'business_display_name',
        'company_id',
        'address1', 
        'address2', 
        'address3',
        'city', 
        'state', 
        'country',
         'pincode',
        'billing_spoc_name', 
        'billing_spoc_contact',
         'billing_spoc_email', 
         'gstin',
         'invoice_email',
        'invoice_cc',
        'support_spoc_name', 
        'support_spoc_mobile',
         'support_spoc_email',
        //  portal access
        'portal_username',
        'portal_password',
        'portal_active',
        'portal_last_login',
         'status',
    ];

    protected $hidden = [
        'portal_password',
        'remember_token',
    ];

    protected $casts = [
        'portal_active' => 'boolean',
        'portal_last_login' => 'datetime',
    ];

    // Relationship with Company
public function company() {
    return $this->belongsTo(Company::class);
}

// Relationship with GSTINs
public function gstins()
{
    return $this->hasMany(Gstin::class, 'entity_id')->where('entity_type', 'client');
}

public function getAuthPassword()
    {
        return $this->portal_password;
    }

    public function links(): HasMany
    {
        return $this->hasMany(ClientLink::class, 'client_id');
    }

    public function notificationSettings()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class);
    }

}
