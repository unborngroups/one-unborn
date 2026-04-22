<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;
use App\Models\UserType;

class SettingsController extends Controller
{
    public function editFeasibilityNotifications()
    {
        $settings = CompanySetting::first();
        $config = $settings->feasibility_notifications ?? [];
        $userTypes = UserType::all();
        return view('settings.feasibility-notifications', compact('config', 'userTypes'));
    }

    public function updateFeasibilityNotifications(Request $request)
    {
        $request->validate([
            'open_user_type' => 'required|string',
            'closed_user_type' => 'required|string',
        ]);
        $settings = CompanySetting::first();
        $settings->feasibility_notifications = [
            'Open' => $request->open_user_type,
            'Closed' => $request->closed_user_type,
        ];
        $settings->save();
        return redirect()->back()->with('status', 'Feasibility notification settings updated!');
    }
}
