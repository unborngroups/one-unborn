<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Helpers\TemplateHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Company;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Load templates with related company
    $templates = EmailTemplate::with('company')->get();

     // ✅ Use the helper correctly
        $permissions = TemplateHelper::getUserMenuPermissions('Template Masters') ?? (object)[
    'can_menu' => true,
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
];

        return view('emails.index', compact('templates', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $companies = Company::all();  // fetch companies
    return view('emails.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
           'company_id' => 'required|exists:companies,id',
           'event_key'  => 'required|string|max:255|unique:email_templates,event_key',
           'subject'    => 'required|string|max:255',
           'body'       => 'required|string',
        ]);
        // Fetch company name automatically
         EmailTemplate::create([
        'company_id' => $request->company_id,
        'event_key'  => $request->event_key,
        'name'       => 'User Created', // ✅ Fix: Set the name field
        'subject'    => $request->subject,
        'body'       => $request->body,
        'status'     => 'Active', // default
    ]);
        return redirect()->route('emails.index')->with('success', 'Template created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($email)
{
    // Fetch the template by ID
    $template = EmailTemplate::findOrFail($email);

    // Fetch all companies (for dropdown)
    $companies = Company::all();

    // Pass both to the view
    return view('emails.edit', compact('template', 'companies'));
}

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $email)
{
    $request->validate([
        'company_id' => 'required|exists:companies,id',
        'event_key'  => 'required|string|max:255|unique:email_templates,event_key,' . $email,
        'subject'    => 'required|string|max:255',
        'body'       => 'required|string',
        'header'     => 'nullable|string',
        'footer'     => 'nullable|string',
    ]);
    $template = EmailTemplate::findOrFail($email);
    $template->update([
        'company_id' => $request->company_id,
        'event_key'  => $request->event_key,
        'name'       => 'User Created', // ✅ Fix: Ensure name field is set
        'subject'    => $request->subject,
        'body'       => $request->body,
        'header'     => $request->header,
        'footer'     => $request->footer,
    ]);
    return redirect()->route('emails.index')->with('success', 'Template updated successfully.');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($email)
    {
        try {
            Log::info('🗑️ Delete request received for template ID: ' . $email);
            
            $emailTemplate = EmailTemplate::findOrFail($email);
            Log::info('📧 Found template: ' . $emailTemplate->subject);
            
            $emailTemplate->delete();
            Log::info('✅ Template deleted successfully');

            return redirect()->route('emails.index')->with('success', 'Template deleted successfully.');
        } catch (\Exception $e) {
            Log::error('❌ Delete failed: ' . $e->getMessage());
            return redirect()->route('emails.index')->with('error', 'Error deleting template: ' . $e->getMessage());
        }
    }

     /**
     * Toggle status Active/Inactive.
     */
    public function toggleStatus($id)
{
    $template = EmailTemplate::findOrFail($id);
    $template->status = $template->status === 'Active' ? 'Inactive' : 'Active';
    $template->save();

    return redirect()->route('emails.index')->with('success', 'Template status updated successfully.');
}


public function sendWelcomeMail($userId)
{
    $user = \App\Models\User::find($userId);
    $company = Company::first();

    // 🟢 Get an active email template
    $template = EmailTemplate::where('status', 'Active')->first();

    if (!$template) {
        return "No active email template found.";
    }

    // 🧩 Replace placeholders inside template body dynamically
    $body = str_replace(
        ['@{{name}}', '@{{company_name}}', '@{{email}}', '@{{joining_date}}'],
        [
            $user->name ?? 'User',
            optional($company)->company_name ?? 'Company',
            // $company->company_name ?? 'Company',
            $user->email ?? '-',
            // $user->joining_date ?? '-'
            $user->Date_of_Joining
                ? \Carbon\Carbon::parse($user->Date_of_Joining)->format('Y-m-d')
                : '-',
        ],
        $template->body
    );
     // 🖼️ Prepare the logo path safely
    $logo = optional($template->company)->billing_logo
        ? asset('storage/' . $template->company->billing_logo)
        : null;

    // 📩 Prepare email data for the Blade view
    $emailData = [
        'name'          => $user->name,
        'email'         => $user->email,
        'joining_date'  => $user->Date_of_Joining
                            ? \Carbon\Carbon::parse($user->Date_of_Joining)->format('Y-m-d')
                            : '-',
        // 'joining_date'  => $user->joining_date,
        'company_name'  => $company->company_name,
        'subject'       => $template->subject,
        'body'          => $body,
        'logo'          => $logo,
    ];

    // 📨 Send email
    Mail::send('emails.welcome', ['emailData' => $emailData], function ($message) use ($user, $emailData) {
        $message->to($user->email)
                ->subject($emailData['subject']);
    });

    return "Welcome email sent successfully to {$user->email}.";
}

}

