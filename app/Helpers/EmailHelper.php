<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailTemplate;

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
    public static function sendDynamicEmail($to, $templateKey, $data = [], $cc = null, $from = null)
    {
        // Fetch template from DB or config
        $template = self::getTemplate($templateKey);
        if (!$template) {
            Log::error("Email template not found: {$templateKey}");
            return false;
        }

        $subject = self::parseTemplate($template->subject, $data);
        $body = self::parseTemplate($template->body, $data);

        try {
            Mail::send([], [], function ($message) use ($to, $cc, $from, $subject, $body) {
                $message->to($to)
                    ->subject($subject)
                    ->html($body);
                if ($cc) {
                    $message->cc($cc);
                }
                if ($from) {
                    $message->from($from);
                }
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            return false;
        }
    }
}
