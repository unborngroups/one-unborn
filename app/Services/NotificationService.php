<?php

namespace App\Services;

use App\Models\NotificationSetting;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\SlaBreachNotification;
use App\Mail\LinkDownAlert;
use App\Mail\HighLatencyAlert;
use App\Mail\HighPacketLossAlert;

class NotificationService
{
    public static function getRecipients($clientLink)
    {
        $settings = NotificationSetting::first();
        $emails = [];

        if ($settings->operations_email) {
            $emails[] = $settings->operations_email;
        }

        if ($clientLink->billing_spoc_email) {
            $emails[] = $clientLink->billing_spoc_email;
        }

        if ($settings->additional_emails) {
            $extra = explode(',', $settings->additional_emails);
            $emails = array_merge($emails, $extra);
        }

        return array_unique($emails);
    }

    private static function send($mailable, $recipients, $eventType, $clientLink)
    {
        foreach ($recipients as $email) {
            Mail::to($email)->send($mailable);
        }

        NotificationLog::create([
            'client_link_id' => $clientLink->id,
            'notification_type' => $eventType,
            'sent_to' => implode(',', $recipients),
        ]);
    }

    public static function sendSlaBreachAlert($clientLink, $report)
    {
        $settings = NotificationSetting::first();
        if (!$settings->sla_breach_alert) return;

        $recipients = self::getRecipients($clientLink);
        self::send(new SlaBreachNotification($clientLink, $report), $recipients, 'SLA Breach', $clientLink);
    }

    public static function sendLinkDownAlert($clientLink)
    {
        $settings = NotificationSetting::first();
        if (!$settings->link_down_alert) return;

        $recipients = self::getRecipients($clientLink);
        self::send(new LinkDownAlert($clientLink), $recipients, 'Link Down', $clientLink);
    }

    public static function sendHighLatencyAlert($clientLink, $latency)
    {
        $settings = NotificationSetting::first();
        if (!$settings->latency_alert) return;

        $recipients = self::getRecipients($clientLink);
        self::send(new HighLatencyAlert($clientLink, $latency), $recipients, 'High Latency', $clientLink);
    }

    public static function sendHighPacketLossAlert($clientLink, $packetLoss)
    {
        $settings = NotificationSetting::first();
        if (!$settings->packet_loss_alert) return;

        $recipients = self::getRecipients($clientLink);
        self::send(new HighPacketLossAlert($clientLink, $packetLoss), $recipients, 'Packet Loss', $clientLink);
    }
}
