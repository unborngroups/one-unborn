<?php

namespace App\Helpers;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class MailHelper
{
     
    
   public static function setMailConfig($companyId = null)
{
    $companyId = $companyId ?: 1;

    // Load company model early
    $company = \App\Models\Company::find($companyId);
    $setting = CompanySetting::where('company_id', $companyId)->first();

    // ✅ PRIORITY 1 — Company Master Email Config
    if ($company && self::hasCompanyMasterConfig($company)) {
        self::setCompanyMasterConfig($company);
        Log::info('📧 Using Company Master email configuration (Priority 1)', [
            'company_id' => $companyId
        ]);
        return;
    }

    // ✅ PRIORITY 2 — Company Settings Email Config
    if ($setting && self::hasCompanySettingConfig($setting)) {
        self::setCompanySettingConfig($setting, $companyId);
        Log::info('📧 Using Company Settings email configuration (Priority 2)', [
            'company_id' => $companyId
        ]);
        return;
    }

    // ✅ PRIORITY 3 — .env fallback
    if (self::hasEnvMailConfig()) {
        Log::info('📧 Using .env mail configuration (Fallback)');
        return; // Laravel will use .env automatically
    }

    // ❌ No config at all
    Log::warning("⚠️ No email configuration found for company {$companyId}");
}

    /**
     * Check if .env has mail configuration
     */
    private static function hasEnvMailConfig()
    {
        return !empty(env('MAIL_HOST')) && 
               !empty(env('MAIL_USERNAME')) && 
               !empty(env('MAIL_PASSWORD'));
    }
    
    /**
     * Check if Company Master has email configuration
     */
    private static function hasCompanyMasterConfig($company)
    {
        // Check if company has email config fields (you may need to add these to companies table)
        return !empty($company->mail_host) && 
               !empty($company->mail_username) && 
               !empty($company->mail_password);
    }

    private static function hasCompanySettingConfig($setting)
    {
        return !empty($setting->mail_host)
            && !empty($setting->mail_username)
            && !empty($setting->mail_password);
    }
    
    /**
     * Set Company Master email configuration
     */
    private static function setCompanyMasterConfig($company)
    {
        // Set default mailer to smtp
        Config::set('mail.default', 'smtp');
        
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $company->mail_host);
        Config::set('mail.mailers.smtp.port', $company->mail_port ?? 587);
        Config::set('mail.mailers.smtp.encryption', $company->mail_encryption ?? 'tls');
        Config::set('mail.mailers.smtp.username', $company->mail_username);
        Config::set('mail.mailers.smtp.password', $company->mail_password);

        Config::set('mail.from.address', $company->mail_from_address);
        Config::set('mail.from.name', $company->mail_from_name ?? $company->company_name);
    }
    
    /**
     * Set Company Settings email configuration
     */
    private static function setCompanySettingConfig($setting, $companyId)
    {
        // Set default mailer to smtp
        Config::set('mail.default', 'smtp');
        
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $setting->mail_host);
        Config::set('mail.mailers.smtp.port', $setting->mail_port ?? 587);
        Config::set('mail.mailers.smtp.encryption', $setting->mail_encryption ?? 'tls');
        Config::set('mail.mailers.smtp.username', $setting->mail_username);
        Config::set('mail.mailers.smtp.password', $setting->mail_password);

        Config::set('mail.from.address', $setting->mail_from_address);
        Config::set('mail.from.name', $setting->mail_from_name ?? 'System');
    }
}
  