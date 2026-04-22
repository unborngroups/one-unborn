<?php

namespace App\Helpers;

use App\Models\WhatsAppSettings;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
   public static function whatsappNotification($message, $phone_number, $document = null)
{
    $settings = WhatsAppSettings::where('is_default', 1)->first();

    if (!$settings) {
        return json_encode(['status' => 'error', 'message' => 'WhatsApp settings not configured']);
    }

    $instanceId  = $settings->unofficial_instance_id;
    $accessToken = $settings->unofficial_access_token;
    $apiUrl      = $settings->unofficial_api_url;

    $whatsapp = preg_replace('/[^0-9]/', '', $phone_number);

    $payload = [
        "number"       => (str_starts_with($whatsapp, "91") ? $whatsapp : "91".$whatsapp),
        "type"         => $document ? "media" : "text",
        "message"      => $message,
        "instance_id"  => $instanceId,
        "access_token" => $accessToken
    ];

    if ($document) {
        $payload["media_url"] = $document;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    Log::info("WA RESPONSE: " . $response);

    return $response;
}
}