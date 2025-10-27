<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        // 🏷️ Basic Details
        'trade_name',
        'company_name',
        'cin_llpin',

        // ☎️ Contact Details
        'company_phone',
        'alternative_contact',
        'email_1',
        'email_2',
        'website',

        // 🏢 Address & Registration
        'gst_no',
        'pan_number',
        'address',

        // 📍 Branch & Social Media
        'branch_locations',
        'instagram',
        'youtube',
        'facebook',
        'linkedin',

        // 🏦 Bank Details
        'acc_number',
        'ifsc_code',
        'branch_name',
        'bank_name',

        // 💳 UPI Details
        'upi_id',
        'upi_number',
        'opening_balance',

        // 🧾 Branding
        'billing_logo',
        'billing_sign_normal',
        'billing_sign_digital',

        // 🎨 Theme
        'color',
        'logo',

        // ⚙️ Status
        'status',
    ];

    // Company.php
public function templates()
{
    return $this->hasMany(EmailTemplate::class, 'company_id');
    // return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id');
}

public function users()
{
    // return $this->hasMany(User::class, 'company_id', 'id');
    
     // Since you’re using the pivot table (company_user)
        return $this->belongsToMany(User::class, 'company_user', 'company_id', 'user_id')
                    ->withTimestamps();
}
// Mutator to format PAN number
public function setPanNumberAttribute($value)
{
    $this->attributes['pan_number'] = $value ? strtoupper(str_replace(' ', '', $value)) : null;
}
}
