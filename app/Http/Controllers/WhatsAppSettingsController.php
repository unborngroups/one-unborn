<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsAppSettings;
use App\Helpers\WhatsAppHelper;
use Illuminate\Support\Facades\Log;

class WhatsAppSettingsController extends Controller
{
    /**
     * Display WhatsApp settings page
     */
    public function index()
    {
        $settings = WhatsAppSettings::where('is_default', 1)->first();

        if (!$settings) {
            $settings = WhatsAppSettings::create(['is_default' => true]);
        }

        return view('settings.whatsapp', compact('settings'));
    }

    /**
     * Update WhatsApp API settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'unofficial_api_url' => 'required|url',
            'unofficial_mobile'      => 'required|string',
            'unofficial_instance_id' => 'required|string',
            'unofficial_access_token' => 'required|string',
        ]);

        $settings = WhatsAppSettings::firstOrCreate(['is_default' => 1]);
$settings->update($validated);


        return redirect()->back()->with('success', 'WhatsApp API settings updated successfully!');
    }

    /**
     * Show test message form
     */
    public function showTestForm()
    {
        return view('settings.whatsapp-test');
    }

    /**
     * Send test WhatsApp message
     */
    public function sendTestMessage(Request $request)
    {
        $validated = $request->validate([
            'mobile' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            // Using the new helper method
            $response = WhatsAppHelper::whatsappNotification(
                $validated['message'],
                $validated['mobile']
            );

            $apiResponse = json_decode($response, true);

            if (isset($apiResponse['status']) && $apiResponse['status'] == 'success') {
                return redirect()->back()
                    ->with('success', 'Test message sent successfully!')
                    ->with('api_response', json_encode($apiResponse, JSON_PRETTY_PRINT));
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to send message')
                    ->with('api_response', json_encode($apiResponse, JSON_PRETTY_PRINT));
            }

        } catch (\Exception $e) {
    Log::error("WhatsApp Send Error: " . $e->getMessage());
    return redirect()->back()
        ->with('error', 'Error: ' . $e->getMessage());
}
    }
    
}
