<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Menu;
use App\Models\UserType;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Helpers\TemplateHelper;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Mail\CustomUserMail;
use App\Helpers\MailHelper;
use Carbon\Carbon;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['userType', 'companies'])->latest()->get();
        $companies = Company::all();
        $usertypes = UserType::all();

        $userType = Auth::user()->userType->name ?? 'user';
        // ✅ Use the helper correctly
        $permissions = TemplateHelper::getUserMenuPermissions('Manage Users') ?? (object)[
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
];

    // 🔍 Find menu for the current page (example: Manage Users)
            $menu = null;
        return view('users.index', compact('users', 'companies', 'usertypes', 'menu', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(User $user)
    {
        $userTypes = UserType::all();
        $companies = Company::all();
        $selectedCompanies = $user->companies->pluck('id')->toArray();

        return view('users.create', compact('user', 'userTypes', 'companies', 'selectedCompanies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name'             => 'required|string|max:255',
        'user_type_id'     => 'required|exists:user_types,id',
        'official_email'   => 'required|email|unique:users,official_email',
        'personal_email'   => 'nullable|email',
        'mobile'           => 'nullable|string|max:15',
        'Date_of_Birth'    => 'required|date',
        'Date_of_Joining'  => 'nullable|date',
        'companies'        => 'required|array',
        'companies.*'      => 'exists:companies,id',
        'status'           => 'required|in:Active,Inactive',
    ]);

    // ✅ Convert dates
    // $dob = Carbon::createFromFormat('d-m-Y', $validated['Date_of_Birth'])->format('Y-m-d');
    // $doj = !empty($validated['Date_of_Joining'])
    //     ? Carbon::createFromFormat('d-m-Y', $validated['Date_of_Joining'])->format('Y-m-d')
    //     : null;
    $dob = Carbon::parse($validated['Date_of_Birth'])->format('Y-m-d');
$doj = !empty($validated['Date_of_Joining'])
    ? Carbon::parse($validated['Date_of_Joining'])->format('Y-m-d')
    : null;

    // ✅ Create the user
     $password = Str::random(10);
    $user = User::create([
        'name'            => $validated['name'],
        'user_type_id'    => $validated['user_type_id'],
        'email'           => $validated['official_email'],
        'official_email' => $validated['official_email'],
        'personal_email' => $validated['personal_email'] ?? null,  
        'mobile'          => $validated['mobile'] ?? null,
        'Date_of_Birth'   => $dob,
        'Date_of_Joining' => $doj,
        'company_id'       => $validated['companies'][0] ?? null, 
        'status'          => $validated['status'],
        'password'        => Hash::make($password),
    ]);
  
    // ✅ Attach selected companies
    $user->companies()->sync($validated['companies']);

    // ✅ Load first assigned company safely
$firstCompanyId = $validated['companies'][0] ?? 1;
$firstCompany = Company::find($firstCompanyId);

// ✅ CASE 1: Look for company-specific template only (no fallback to global)
$template = EmailTemplate::where('company_id', $firstCompanyId)
    ->where('name', 'User Created')
    ->first();

// ✅ CASE 2: If no template exists, use system default content (don't create/store template)
$useSystemDefault = false;
if (!$template) {
    Log::info('🔄 No template found for company: ' . $firstCompanyId . ' - Using system default welcome email');
    $useSystemDefault = true;
    
    // Create virtual template object with system default content
    $template = (object) [
        'subject' => 'Welcome to {company_name}, {name}!',
        'body' => 'Hello {name},<br><br>Welcome to {company_name}!<br><br>Your login credentials:<br>Email: {email}<br>Password: {password}<br><br>Please login and update your profile.<br><br>Best regards,<br>Team {company_name}'
    ];
}

Log::info('🟡 Email Template Check:', [
    'template_found' => (bool)$template,
    'company_id' => $firstCompanyId,
    'template_id' => $useSystemDefault ? 'system_default' : ($template ? $template->id : null),
     'template_source' => $useSystemDefault ? 'System Default' : 'Template Master',
    'template_body_preview' => $template ? substr($template->body, 0, 100) : 'No template found'
]);
        // ✅ Replace placeholders in email
        $body = str_replace(
            ['{name}', '{company_name}', '{email}', '{joining_date}', '{password}'],
            [
                $user->name,
                optional($firstCompany)->company_name,
                $user->official_email,
                $user->Date_of_Joining,
                $password
            ],
            $template->body
        );

        $subject = str_replace(
            ['{name}', '{company_name}', '{email}', '{joining_date}'],
            [
                $user->name,
                optional($firstCompany)->company_name,
                $user->official_email,
                $user->Date_of_Joining
            ],
            $template->subject
        );

        // ✅ Prepare email data
        $emailData = [
            'name'         => $user->name,
            'company'      => optional($firstCompany)->company_name,
            'email'        => $user->official_email,
            'joining_date' => $user->Date_of_Joining,
            'body'         => $body,
            'subject'      => $subject,
            'logo'         => optional($firstCompany)->billing_logo
                ? asset('storage/' . $firstCompany->billing_logo)
                : null,
            'password'     => $password,
        ];

        // ✅ Only add template_body for CASE 1 (Template Master exists)
        if (!$useSystemDefault) {
            $emailData['template_body'] = $body; // Add processed Template Master content
        }
        // For CASE 2 (System Default), don't add template_body - this will show clean default layout

        // ✅ Load mail config for this company
        $companySetting = CompanySetting::where('company_id', $firstCompanyId)->first()
            ?? CompanySetting::find(1);

        if ($companySetting) {
            MailHelper::setMailConfig($firstCompanyId); // must set config keys
        // Log::info('📧 Mail config applied', ['company_id' => $firstCompanyId]);
    } else {
        Log::warning('⚠️ CompanySetting not found; using default mail config.');
        Log::info('📧 Mail config applied', [
    'company_id' => $firstCompanyId,
    'mailer' => config('mail.mailers.smtp.transport'),
    'host' => config('mail.mailers.smtp.host')
]);

    }

    // ✅ Pick correct email priority: official > personal > general
if (!empty($user->official_email) && filter_var($user->official_email, FILTER_VALIDATE_EMAIL)) {
    $toEmail = $user->official_email;
} elseif (!empty($user->personal_email) && filter_var($user->personal_email, FILTER_VALIDATE_EMAIL)) {
    $toEmail = $user->personal_email;
} elseif (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
    $toEmail = $user->email;
} else {
    Log::error("❌ No valid recipient email found for user ID {$user->id}");
    return redirect()->route('users.index')->with('warning', 'User created but no valid email found.');
}

try {
    Mail::to($toEmail)->send(new CustomUserMail($emailData));
    Log::info("✅ Welcome email sent successfully to {$toEmail}");
} catch (\Exception $e) {
    Log::error("❌ Mail sending failed for {$toEmail}: " . $e->getMessage());
}


        return redirect()->route('users.index')
            ->with('success', 'User created successfully and welcome email sent!');
    }


    /**
     * Display the specified resource.
     */
   public function view($id)
{
    $user = \App\Models\User::with(['userType', 'companies', 'profile'])->findOrFail($id);
    
    return view('users.view', compact('user'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $userTypes = UserType::all();
        $companies = Company::all();
        $selectedCompanies = $user->companies->pluck('id')->toArray();

        return view('users.edit', compact('user', 'userTypes', 'companies', 'selectedCompanies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
         $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'user_type_id'     => 'required|exists:user_types,id',
            'official_email'   => 'required|email|unique:users,official_email,' . $user->id,
        'personal_email'   => 'nullable|email',
            'mobile'           => 'nullable|string|max:15',
            'Date_of_Birth'    => 'required|date',
            'Date_of_Joining'  => 'nullable|date',
            'companies' => 'required|array',
            'companies.*' => 'exists:companies,id',
            'status'           => 'required|in:Active,Inactive',
        ]);
         $dob = $request->Date_of_Birth 
        ? Carbon::createFromFormat('d-m-Y', $request->Date_of_Birth)->format('Y-m-d') 
        : null;

    $doj = $request->Date_of_Joining 
        ? Carbon::createFromFormat('d-m-Y', $request->Date_of_Joining)->format('Y-m-d') 
        : null;


        $user->update([
            'name'             => $validated['name'],
            'user_type_id'     => $validated['user_type_id'],
            'official_email'   => $validated['official_email'],
        'personal_email'   => $validated['personal_email'] ?? null,
            'mobile'           => $validated['mobile'] ?? null,
            'Date_of_Birth'    => $dob,
            'Date_of_Joining'  => $doj,
            'status'           => $validated['status'],
            'email'            => $validated['official_email'],// keep email in sync
        ]);

        // Sync company assignments
         $user->companies()->sync($validated['companies']);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->companies()->detach(); // cleanup pivot table
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle status Active/Inactive.
     */
    public function toggleStatus($id)
    {
        $user = Auth::user();

    // Allow SuperAdmin or Admin only
    if (!in_array($user->userType->name, ['superadmin', 'admin'])) {
        abort(403, 'Unauthorized action.');
    }

         $targetUser = User::findOrFail($id);
    $newStatus = $targetUser->status === 'Active' ? 'Inactive' : 'Active';
    $targetUser->update(['status' => $newStatus]);
        return redirect()->route('users.index')->with('success', 'User status updated successfully.');
    }

}
