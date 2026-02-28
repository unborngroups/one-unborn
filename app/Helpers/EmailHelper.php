<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Config;
use App\Models\Company;

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
        self::setMailConfig($mailType);
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
            Mail::send([], [], function ($message) use ($to, $cc, $from, $subject, $dataForTemplate, $template, $inlineAttachments) {
                $message->to($to)
                    ->subject($subject);
                    // ->html('');
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
    $company = Company::with('settings')->first();
    if (!$company || !$company->settings) {
        Log::error('Company or Company settings missing');
        return false;
    }

    $s = $company->settings;

    switch ($type) {
        case 'delivery':
            $host = $s->delivery_mail_host;
            $username = $s->delivery_mail_username;
            $password = $s->delivery_mail_password;
            $port = $s->delivery_mail_port;
            $encryption = $s->delivery_mail_encryption;
            $fromAddress = $s->delivery_mail_from_address;
            $fromName = $s->delivery_mail_from_name ?? 'Unborn';
            break;

        case 'invoice':
            $host = $s->invoice_mail_host;
            $username = $s->invoice_mail_username;
            $password = $s->invoice_mail_password;
            $port = $s->invoice_mail_port;
            $encryption = $s->invoice_mail_encryption;
            $fromAddress = $s->invoice_mail_from_address;
            $fromName = $s->invoice_mail_from_name ?? 'Unborn';
            break;

        default:
            $host = $s->mail_host;
            $username = $s->mail_username;
            $password = $s->mail_password;
            $port = $s->mail_port;
            $encryption = $s->mail_encryption;
            $fromAddress = $s->mail_from_address;
            $fromName = $s->mail_from_name ?? 'Unborn';
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