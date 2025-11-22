<?php

namespace App\Helpers;

class WhatsAppHelper
{
    /**
     * Send WhatsApp Text Message (Senior's exact implementation)
     */
    public static function sendMessage($mobile, $message)
    {
        $instance_id = '691AEEF33256E';
        $access_token = '68f9df1ac354c';
        
        $tmsg = urlencode($message);
        $url = "https://wahub.pro/api/send?number={$mobile}&type=text&message={$tmsg}&instance_id={$instance_id}&access_token={$access_token}";

        return self::sendCurlRequest($url, $mobile, 'text');
    }

    /**
     * Send WhatsApp Media Message (Senior's exact implementation)
     */
    public static function sendMediaMessage($mobile, $message, $mediaUrl, $filename)
    {
        $instance_id = '691AEEF33256E';
        $access_token = '68f9df1ac354c';
        
        $tmsg = urlencode($message);
        $url = "https://wahub.pro/api/send?number={$mobile}&type=media&message={$tmsg}&media_url={$mediaUrl}&filename={$filename}&instance_id={$instance_id}&access_token={$access_token}";

        return self::sendCurlRequest($url, $mobile, 'media');
    }

    /**
     * Send cURL request (Senior's exact implementation)
     */
    private static function sendCurlRequest($url, $toNumber, $typeOfMsg)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        
        // Debug: Log raw response (using storage path)
        $logPath = storage_path('logs/whatsapp_debug.txt');
        $logContent = date("Y-m-d H:i:s") . "\n" .
            "URL: " . $url . "\n" .
            "HTTP Code: " . $httpCode . "\n" .
            "cURL Error: " . $curlError . "\n" .
            "Raw Response: " . $response . "\n" .
            str_repeat("-", 80) . "\n";
        
        file_put_contents($logPath, $logContent, FILE_APPEND);
        
        $whatsappResponse = json_decode($response, true);
        
        // Log to file (following senior's pattern) - using storage path
        $status = isset($whatsappResponse['status']) ? $whatsappResponse['status'] : 'unknown';
        $msg = isset($whatsappResponse['message']) ? $whatsappResponse['message'] : 'no message';
        $content = date("Y-m-j:g:ia") . ':to:' . $toNumber . ';type:' . $typeOfMsg . ';Status: ' . $status . ';Message:' . $msg . ';';

        $logFile = storage_path('logs/whatsapp_log.txt');
        file_put_contents($logFile, $content . PHP_EOL, FILE_APPEND);

        return $response;
    }

    /**
     * Send WhatsApp Document
     */
    public static function sendDocument($mobile, $message, $documentUrl, $filename)
    {
        return self::sendMediaMessage($mobile, $message, $documentUrl, $filename);
    }

    /**
     * Send WhatsApp Image
     */
    public static function sendImage($mobile, $message, $imageUrl)
    {
        return self::sendMediaMessage($mobile, $message, $imageUrl, '');
    }
}
