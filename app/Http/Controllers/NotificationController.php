<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotificationSetting;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function settings()
    {
        $client = Auth::guard('client')->user();
        $settings = NotificationSetting::firstOrCreate(['client_id' => $client->id]);
        return view('client_portal.notifications.settings', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        $client = Auth::guard('client')->user();

        NotificationSetting::updateOrCreate(
            ['client_id' => $client->id],
            $request->all()
        );

        return back()->with('success', 'Notification settings saved');
    }

    public function logs()
    {
        $client = Auth::guard('client')->user();
        $logs = NotificationLog::where('client_id', $client->id)
            ->orderBy('sent_at', 'desc')
            ->paginate(50);

        return view('client_portal.notifications.logs', compact('logs'));
    }
}
