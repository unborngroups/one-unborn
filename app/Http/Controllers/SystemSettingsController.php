<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        return view('settings.system', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'timezone' => 'required',
            'date_format' => 'required',
            'language' => 'required',
            'currency_symbol' => 'required',
            'fiscal_start_month' => 'required',
            'surepass_api_token' => 'nullable|string',
        ]);

        SystemSetting::updateOrCreate(['id' => 1], $request->all());

        return redirect()->back()->with('success', 'System settings updated successfully!');
    }

}
