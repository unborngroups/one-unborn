<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
     use HasFactory;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($vendor) {
            if (empty($vendor->vendor_code)) {
                $vendor->vendor_code = \App\Services\PrefixGenerator::generateVendorCode();
            }
        });
    }

    protected $fillable = [
        'user_name',
        'vendor_code',
        'vendor_name',
        'business_display_name',
        'address1',
        'address2',
        'address3',
        'city',
        'state',
        'country',
        'pincode',
        
        
        // 'invoice_email',
        // 'invoice_cc',

        // 'product_category',
        'contact_person_name',
        'contact_person_mobile',
        'contact_person_email',
        'gstin',
        'pan_no',
        // 'product_category',
    // 'make_id',
    // 'company_name',
    // 'make_contact_no',
    // 'make_email',
    // 'model_no',
    // 'serial_no',
    // 'asset_id',

        'branch_name',
        'bank_name',
        'bank_account_no',
        'ifsc_code',
        'status',
    ];

    // Relationship with GSTINs
    public function gstins()
    {
        return $this->hasMany(Gstin::class, 'entity_id')->where('entity_type', 'vendor');
    }
    public function make()
{
    return $this->belongsTo(\App\Models\VendorMake::class, 'make_id');
}

}
