<?php

namespace App\Helpers;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    /**
     * Set mail configuration for a company (priority: Company Master -> CompanySetting -> .env)
     * Returns true if a mail config was applied (or .env fallback present), false otherwise.
     */
    public static function setMailConfig($companyId = null): bool
    {
        $companyId = $companyId ?: 1;

        // Load models
        $company = \App\Models\Company::find($companyId);
        $setting = CompanySetting::where('company_id', $companyId)->first();

        // Priority 1: Company master config
        if ($company && self::hasCompanyMasterConfig($company)) {
            self::applySmtpConfig([
                'host'       => $company->mail_host,
                'port'       => $company->mail_port ?? 587,
                'encryption' => $company->mail_encryption ?? 'tls',
                'username'   => $company->mail_username,
                'password'   => $company->mail_password,
                'from'       => [
                    'address' => $company->mail_from_address ?? $company->company_email ?? null,
                    'name'    => $company->mail_from_name ?? $company->company_name ?? 'Application',
                ],
                'allow_self_signed' => $company->mail_allow_self_signed ?? false,
            ]);

            Log::info('📧 Using Company Master email configuration (Priority 1)', ['company_id' => $companyId]);
            return true;
        }

        // Priority 2: CompanySetting config
        if ($setting && self::hasCompanySettingConfig($setting)) {
            self::applySmtpConfig([
                'host'       => $setting->mail_host,
                'port'       => $setting->mail_port ?? 587,
                'encryption' => $setting->mail_encryption ?? 'tls',
                'username'   => $setting->mail_username,
                'password'   => $setting->mail_password,
                'from'       => [
                    'address' => $setting->mail_from_address ?? null,
                    'name'    => $setting->mail_from_name ?? 'System',
                ],
                'allow_self_signed' => $setting->mail_allow_self_signed ?? false,
            ]);

            Log::info('📧 Using Company Settings email configuration (Priority 2)', ['company_id' => $companyId]);
            return true;
        }

        // Priority 3: .env fallback (Laravel will already use .env)
        if (self::hasEnvMailConfig()) {
            Log::info('📧 Using .env mail configuration (Fallback)');
            // still purge to ensure runtime reload if necessary
            Mail::purge();
            return true;
        }

        Log::warning("⚠ No email configuration found for company {$companyId}");
        return false;
    }

    /**
     * Apply SMTP config and purge mailer so new settings take effect immediately.
     * Expects array with host, port, encryption, username, password, from array, allow_self_signed bool.
     */
    private static function applySmtpConfig(array $params): void
    {
        $host = $params['host'] ?? null;
        $port = intval($params['port'] ?? 587);
        $encryption = $params['encryption'] ?? 'tls';
        $username = $params['username'] ?? null;
        $password = $params['password'] ?? null;
        $from = $params['from'] ?? ['address' => null, 'name' => 'Application'];
        $allowSelfSigned = !empty($params['allow_self_signed']);

        if (empty($host) || empty($username) || empty($password)) {
            Log::warning('MailHelper: skipping applySmtpConfig because of missing host/username/password');
            return;
        }

        // Build mailer config array (Laravel mail.php structure)
        $smtpConfig = [
            'transport' => 'smtp',
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption === 'ssl' ? 'ssl' : ($encryption === 'tls' ? 'tls' : null),
            'username' => $username,
            'password' => $password,
            'timeout' => null,
            'auth_mode' => null,
        ];

        // Optional stream options for servers with self-signed certs (use only for testing)
        $stream = null;
        if ($allowSelfSigned) {
            $stream = [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ];
            // Put stream into the transport config used by SwiftMailer/Symfony Mailer if needed
            $smtpConfig['stream'] = $stream;
        }

        // Apply config
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', $smtpConfig);

        // Ensure "from" set as array
        if (!empty($from['address'])) {
            Config::set('mail.from', [
                'address' => $from['address'],
                'name' => $from['name'] ?? $from['address'],
            ]);
        }

        // Purge mailer so it rebuilds with new config
        try {
            Mail::purge();
        } catch (\Throwable $e) {
            // if purge not available for some reason, log and continue
            Log::warning('MailHelper: Mail::purge() failed: ' . $e->getMessage());
        }

        Log::info('MailHelper: SMTP configuration applied for host ' . $host);
    }

    private static function hasEnvMailConfig(): bool
    {
        return !empty(env('MAIL_HOST')) && !empty(env('MAIL_USERNAME')) && !empty(env('MAIL_PASSWORD'));
    }

    private static function hasCompanyMasterConfig($company): bool
    {
        return !empty($company->mail_host) && !empty($company->mail_username) && !empty($company->mail_password);
    }

    private static function hasCompanySettingConfig($setting): bool
    {
        return !empty($setting->mail_host) && !empty($setting->mail_username) && !empty($setting->mail_password);
    }
}