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

        // General Notification SMTP
        'general_mail_host',
        'general_mail_port',
        'general_mail_username',
        'general_mail_password',
        'general_mail_encryption',
        'general_mail_from_address',
        'general_mail_from_name',
        'general_mail_footer',
        'general_mail_signature',

        // Delivery Notification SMTP
        'delivery_mail_host',
        'delivery_mail_port',
        'delivery_mail_username',
        'delivery_mail_password',
        'delivery_mail_encryption',
        'delivery_mail_from_address',
        'delivery_mail_from_name',
        'delivery_mail_footer',
        'delivery_mail_signature',
        
        'delivery_email_check',

        // Invoice Sending SMTP
        'invoice_mail_host',
        'invoice_mail_port',
        'invoice_mail_username',
        'invoice_mail_password',
        'invoice_mail_encryption',
        'invoice_mail_from_address',
        'invoice_mail_from_name',
        'invoice_mail_footer',
        'invoice_mail_signature',
        
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
