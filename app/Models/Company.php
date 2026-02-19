<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Feasibility;
use App\Models\Client;
use App\Models\Invoice; 

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        // 🏷️ Basic Details
        'user_name',
        'trade_name',
        'company_name',
        'business_number',

        // ☎️ Contact Details
        'company_phone',
        'alternative_contact_number',
        'company_email',
        // 'secondary_email',
        'website',

        // 🏢 Address & Registration
        'gstin',
        'pan_number',
        'address',

        // 📍 Branch & Social Media
        'branch_location',
        'store_location_url',
        'google_place_id',
        'instagram',
        'youtube',
        'facebook',
        'linkedin',

        // 🏦 Bank Details
        'account_number',
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

        // EMAIL CONFIG (NEW ✅)
    // 'mail_mailer',
    'mail_host',
    'mail_port',
    'mail_username',
    'mail_password',
    'mail_encryption',
    'mail_from_address',
    'mail_from_name',
    'mail_footer',
    'mail_signature',

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
// Relationships
public function feasibilities() {
    return $this->hasMany(Feasibility::class);
}
public function clients() {
    return $this->hasMany(Client::class);
}

   // Relationship to company settings (for SMTP etc)
    public function settings()
    {
        return $this->hasOne(\App\Models\CompanySetting::class, 'company_id', 'id');
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }   
}

