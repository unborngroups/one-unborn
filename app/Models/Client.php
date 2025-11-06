<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   use HasFactory;

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
         'status',
    ];
    // Relationship with Company
public function company() {
    return $this->belongsTo(Company::class);
}


}
