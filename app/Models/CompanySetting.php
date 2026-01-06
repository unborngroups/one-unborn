<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_id',
        'company_name',
        'company_email',
        'exception_permission_email',
        'contact_no',
        'website',
        'address',
        'gst_number',
        'company_logo',
        'linkedin_url',
        'facebook_url',
        'instagram_url',
        'whatsapp_number',
        'is_default',
        'feasibility_notifications',
        // Email Settings
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
        
    ];


   protected $casts = [
        'is_default' => 'boolean',
        'feasibility_notifications' => 'array',
    ];

    public function templates()
    {
        return $this->hasMany(EmailTemplate::class, 'company_id', 'company_id');
    }

}
