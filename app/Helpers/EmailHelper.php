<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Config;
use App\Models\CompanySetting;

class EmailHelper
{
    /**
     * Fetch the email template by key from the database.
     * @param string $templateKey
     * @return EmailTemplate|null
     */
    public static function getTemplate($templateKey)
    {
        return EmailTemplate::where('event_key', $templateKey)->first();
    }

    /**
     * Replace placeholders in the template with data.
     * @param string $content
     * @param array $data
     * @return string
     */
    public static function parseTemplate($content, $data = [])
    {
        return \App\Helpers\TemplateHelper::renderTemplate($content, $data);
    }
    /**
     * Send a dynamic email using a template.
     * @param string|array $to
     * @param string $templateKey
     * @param array $data
     * @param string|array|null $cc
     * @param string|null $from
     * @return bool
     */
    public static function sendDynamicEmail($to, $templateKey, $data = [], $cc = null, $from = null, $mailType = 'main')
    {
        if (!self::setMailConfig($mailType)) {
            Log::error('Dynamic mail config failed', ['mail_type' => $mailType]);
            return false;
        }
        // Fetch template from DB or config
        $template = self::getTemplate($templateKey);
        if (!$template) {
            Log::error("Email template not found: {$templateKey}");
            return false;
        }

        $dataForTemplate = $data;
        $inlineAttachments = $dataForTemplate['_inline_attachments'] ?? [];
        unset($dataForTemplate['_inline_attachments']);
        $subject = self::parseTemplate($template->subject, $dataForTemplate);

        try {
            Mail::html('', function ($message) use ($to, $cc, $from, $subject, $dataForTemplate, $template, $inlineAttachments) {
                $message->to($to)
                    ->subject($subject);

                if ($cc) {
                    $message->cc($cc);
                }
                if ($from) {
                    $message->from($from);
                }
                $inlineCids = [];
                foreach ($inlineAttachments as $attachment) {
                    $path = $attachment['path'] ?? null;
                    if ($path && file_exists($path)) {
                        $cid = $message->embed($path);
                        $placeholder = $attachment['field'] ?? basename($path);
                        $inlineCids[$placeholder] = $cid;
                    }
                }
                // Replace image URLs in template body with CIDs if present
                $bodyData = array_merge($dataForTemplate, $inlineCids);
                $body = self::parseTemplate($template->body, $bodyData);

                // Some templates are stored with escaped HTML in DB; decode before send.
                $body = html_entity_decode((string) $body, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                // Replace any original URL placeholders with cid when present.
                foreach ($inlineCids as $field => $cid) {
                    $original = (string) ($dataForTemplate[$field] ?? '');
                    if ($original !== '') {
                        $body = str_replace($original, $cid, $body);
                    }
                }
                // if (!empty($inlineCids)) {
                //     foreach ($inlineCids as $field => $cid) {
                //         // Replace src="...field..." with src="cid:..."
                //         $body = preg_replace(
                //        '/src=("|\')([^"\']*'.preg_quote($field, '/').'.*?)("|\')/i',
                //        'src="'.$cid.'"',
                //        $body
                //        );
                //        }
                // }

                
                // $message->setBody(new \Symfony\Component\Mime\Part\TextPart($body, 'utf-8', 'html'));
                $message->html($body);
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            return false;
        }
    }

private static function setMailConfig($type = 'main')
{
    $s = CompanySetting::query()
        ->orderByDesc('is_default')
        ->orderBy('id')
        ->first();

    if (!$s) {
        Log::error('Company settings missing');
        return false;
    }

    switch ($type) {
        case 'delivery':
            $host = $s->delivery_mail_host ?: $s->mail_host ?: env('MAIL_HOST');
            $username = $s->delivery_mail_username ?: $s->mail_username ?: env('MAIL_USERNAME');
            $password = $s->delivery_mail_password ?: $s->mail_password ?: env('MAIL_PASSWORD');
            $port = $s->delivery_mail_port ?: $s->mail_port ?: env('MAIL_PORT');
            $encryption = $s->delivery_mail_encryption ?: $s->mail_encryption ?: env('MAIL_ENCRYPTION', 'tls');
            $fromAddress = $s->delivery_mail_from_address ?: $s->mail_from_address ?: env('MAIL_FROM_ADDRESS');
            $fromName = $s->delivery_mail_from_name ?: $s->mail_from_name ?: env('MAIL_FROM_NAME', 'Unborn');
            break;

        case 'invoice':
            $host = $s->invoice_mail_host ?: $s->mail_host ?: env('MAIL_HOST');
            $username = $s->invoice_mail_username ?: $s->mail_username ?: env('MAIL_USERNAME');
            $password = $s->invoice_mail_password ?: $s->mail_password ?: env('MAIL_PASSWORD');
            $port = $s->invoice_mail_port ?: $s->mail_port ?: env('MAIL_PORT');
            $encryption = $s->invoice_mail_encryption ?: $s->mail_encryption ?: env('MAIL_ENCRYPTION', 'tls');
            $fromAddress = $s->invoice_mail_from_address ?: $s->mail_from_address ?: env('MAIL_FROM_ADDRESS');
            $fromName = $s->invoice_mail_from_name ?: $s->mail_from_name ?: env('MAIL_FROM_NAME', 'Unborn');
            break;

        default:
            $host = $s->mail_host ?: env('MAIL_HOST');
            $username = $s->mail_username ?: env('MAIL_USERNAME');
            $password = $s->mail_password ?: env('MAIL_PASSWORD');
            $port = $s->mail_port ?: env('MAIL_PORT');
            $encryption = $s->mail_encryption ?: env('MAIL_ENCRYPTION', 'tls');
            $fromAddress = $s->mail_from_address ?: env('MAIL_FROM_ADDRESS');
            $fromName = $s->mail_from_name ?: env('MAIL_FROM_NAME', 'Unborn');
    }

    if (!$host || !$username || !$password || !$port || !$fromAddress) {
        Log::error('Mail config missing required fields', compact(
            'host','username','password','port','fromAddress'
        ));
        return false;
    }

    config([
        'mail.default' => 'smtp',
        'mail.mailers.smtp.transport' => 'smtp',
        'mail.mailers.smtp.host' => $host,
        'mail.mailers.smtp.port' => (int)$port,
        'mail.mailers.smtp.encryption' => strtolower($encryption),
        'mail.mailers.smtp.username' => $username,
        'mail.mailers.smtp.password' => $password,
        'mail.mailers.smtp.timeout' => 20,
        'mail.from.address' => $fromAddress,
        'mail.from.name' => $fromName,
    ]);

    app()->forgetInstance('mail.manager');
    app()->forgetInstance('mailer');
    Mail::purge('smtp');

    return true;
}

private static function normalizeEmails($emails)
{
    if (!$emails) return [];

    if (is_array($emails)) return $emails;

    return array_values(array_filter(
        array_map('trim', preg_split('/[,;]/', $emails))
    ));
}

}