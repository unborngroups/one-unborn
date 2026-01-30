<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanySetting;

class CompanySettingsController extends Controller
{
    public function index()
    {
          $company = CompanySetting::firstOrNew(['id' => 1]);
        return view('settings.company', compact('company'));
    }


    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'nullable|email',
            'contact_no' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:50',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'linkedin_url' => 'nullable|url',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'whatsapp_number' => 'nullable|string|max:20',
            // ✅ Email Settings
            'mail_host' => 'nullable|string|max:100',
            'mail_port' => 'nullable|string|max:10',
            'mail_username' => 'nullable|string|max:100',
            'mail_password' => 'nullable|string|max:100',
            'mail_encryption' => 'nullable|string|max:10',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string|max:100',
            'mail_footer' => 'nullable|string|max:500',
            'mail_signature' => 'nullable|string|max:500',
            'exception_permission_email' => 'nullable|email',
            // Add validation for feasibility_notifications
            'feasibility_notifications.Open' => 'nullable|string|max:255',

            // General Notification SMTP
            'general_mail_host' => 'nullable|string|max:100',
            'general_mail_port' => 'nullable|string|max:10',
            'general_mail_username' => 'nullable|string|max:100',
            'general_mail_password' => 'nullable|string|max:100',
            'general_mail_encryption' => 'nullable|string|max:10',
            'general_mail_from_address' => 'nullable|email',
            'general_mail_from_name' => 'nullable|string|max:100',
            'general_mail_footer' => 'nullable|string|max:500',
            'general_mail_signature' => 'nullable|string|max:500',

            // Delivery Notification SMTP
            'delivery_mail_host' => 'nullable|string|max:100',
            'delivery_mail_port' => 'nullable|string|max:10',
            'delivery_mail_username' => 'nullable|string|max:100',
            'delivery_mail_password' => 'nullable|string|max:100',
            'delivery_mail_encryption' => 'nullable|string|max:10',
            'delivery_mail_from_address' => 'nullable|email',
            'delivery_mail_from_name' => 'nullable|string|max:100',
            'delivery_mail_footer' => 'nullable|string|max:500',
            'delivery_mail_signature' => 'nullable|string|max:500',

            // Invoice Sending SMTP
            'invoice_mail_host' => 'nullable|string|max:100',
            'invoice_mail_port' => 'nullable|string|max:10',
            'invoice_mail_username' => 'nullable|string|max:100',
            'invoice_mail_password' => 'nullable|string|max:100',
            'invoice_mail_encryption' => 'nullable|string|max:10',
            'invoice_mail_from_address' => 'nullable|email',
            'invoice_mail_from_name' => 'nullable|string|max:100',
            'invoice_mail_footer' => 'nullable|string|max:500',
            'invoice_mail_signature' => 'nullable|string|max:500',
        ]);
        

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }

        // Save feasibility_notifications as array
        $data['feasibility_notifications'] = $request->input('feasibility_notifications', []);

    // Create or update record
    CompanySetting::updateOrCreate(['id' => 1], $data);
    

        return back()->with('success', 'Company settings updated successfully!');
    }

}
