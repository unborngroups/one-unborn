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
use App\Models\UserTypeMenuPrivilege;
use App\Models\UserMenuPrivilege;


class UserController extends Controller
{
    // ...existing code...
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
     $users = User::orderBy('id', 'desc')->get();

        $users = User::with(['userType', 'companies'])->orderBy('id', 'desc')->get();
        $companies = Company::orderBy('id', 'desc')->get();
        $usertypes = UserType::orderBy('id', 'desc')->get();

        // ✅ Use the helper to get actual permissions from database
        $permissions = TemplateHelper::getUserMenuPermissions('Manage User')?? (object)[
            'can_menu' => true,
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
     ];

        $perPage = $request->input('per_page', 10); // default 10
        $perPage = in_array($perPage, [10,25,50,100]) ? $perPage : 10;
        // Ensure $users is a query builder before paginate
        $usersQuery = User::orderBy('id', 'desc');
        // Apply any filters here if needed
        $users = $usersQuery->paginate($perPage);
        return view('users.index', compact('users', 'companies', 'usertypes', 'permissions'));
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
        'official_email'  => 'required|email|unique:users,email',
        'personal_email'   => 'nullable|email',
        'mobile'           => 'nullable|string|max:15',
        'Date_of_Birth'    => 'required|date',
        'Date_of_Joining'  => 'nullable|date',
        'companies'        => 'required|array',
        'companies.*'      => 'exists:companies,id',
        'status'           => 'required|in:Active,Inactive',
    ]);

    // ✅ Convert dates
   
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
    
    // ✅ AUTO-INHERIT USER TYPE PRIVILEGES
    // When a user is created with a specific user type, automatically assign
    // the default privileges configured for that user type
    $userTypePrivileges = UserTypeMenuPrivilege::where('user_type_id', $validated['user_type_id'])->get();
    
    foreach ($userTypePrivileges as $typePriv) {
        UserMenuPrivilege::create([
            'user_id' => $user->id,
            'menu_id' => $typePriv->menu_id,
            'can_menu' => $typePriv->can_menu,
            'can_add' => $typePriv->can_add,
            'can_edit' => $typePriv->can_edit,
            'can_delete' => $typePriv->can_delete,
            'can_view' => $typePriv->can_view,
        ]);
    }
    
    Log::info("✅ User privileges inherited from user type. Assigned {$userTypePrivileges->count()} privilege records to user: {$user->name}");

    // ✅ Load first assigned company safely
$firstCompanyId = $validated['companies'][0] ?? 1;
$firstCompany = Company::find($firstCompanyId);

// ✅ CASE 1: Look for company-specific template only (no fallback to global)
$template = EmailTemplate::where('company_id', $firstCompanyId) 
    ->first();
    // ?? EmailTemplate::where('company_id', 0)->where('name', 'User Created')->first();
    
Log::info('🟡 Email Template Check:', ['template_found' => (bool)$template]);



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
            'mail_signature' => $companySetting->mail_signature 
                    ?? $firstCompany->mail_signature 
                    ?? null,
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
            if ($companySetting && $companySetting->mail_host) {
    // Use company settings table
    MailHelper::setMailConfig($firstCompanyId);
} else {
    // Use company master config
    MailHelper::setMailConfig($firstCompanyId);
}
 // must set config keys
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
            'official_email'   => 'required|email|unique:users,email,' . $user->id,
        'personal_email'   => 'nullable|email',
            'mobile'           => 'nullable|string|max:15',
            'Date_of_Birth'    => 'required|date',
            'Date_of_Joining'  => 'nullable|date',
            'companies' => 'required|array',
            'companies.*' => 'exists:companies,id',
            'status'           => 'required|in:Active,Inactive',
        ]);

        $dob = null;
        if ($request->Date_of_Birth) {
            try {
                $dob = Carbon::createFromFormat('d-m-Y', $request->Date_of_Birth)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    $dob = Carbon::createFromFormat('Y-m-d', $request->Date_of_Birth)->format('Y-m-d');
                } catch (\Exception $e2) {
                    $dob = null;
                }
            }
        }

        $doj = null;
        if ($request->Date_of_Joining) {
            try {
                $doj = Carbon::createFromFormat('d-m-Y', $request->Date_of_Joining)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    $doj = Carbon::createFromFormat('Y-m-d', $request->Date_of_Joining)->format('Y-m-d');
                } catch (\Exception $e2) {
                    $doj = null;
                }
            }
        }

        // ✅ Check if any field that requires email has changed
$shouldSendEmail = false;

// If email changed
if ($user->official_email !== $validated['official_email']) {
    $shouldSendEmail = true;
}

// If status changed
if ($user->status !== $validated['status']) {
    $shouldSendEmail = true;
}

// If company changed
if (!empty(array_diff($validated['companies'], $user->companies->pluck('id')->toArray()))) {
    $shouldSendEmail = true;
}

        $previousUserTypeId = $user->user_type_id;

        // Only update password if sending email (i.e., new password generated), else keep old password
        if ($request->has('send_email')) {
            $password = Str::random(10);
            $user->update([
                'name'             => $validated['name'],
                'user_type_id'     => $validated['user_type_id'],
                'official_email'   => $validated['official_email'],
                'personal_email'   => $validated['personal_email'] ?? null,
                'mobile'           => $validated['mobile'] ?? null,
                'Date_of_Birth'    => $dob,
                'Date_of_Joining'  => $doj,
                'status'           => $validated['status'],
                'email'            => $validated['official_email'],
                'password'         => Hash::make($password),
            ]);
        } else {
            $user->update([
                'name'             => $validated['name'],
                'user_type_id'     => $validated['user_type_id'],
                'official_email'   => $validated['official_email'],
                'personal_email'   => $validated['personal_email'] ?? null,
                'mobile'           => $validated['mobile'] ?? null,
                'Date_of_Birth'    => $dob,
                'Date_of_Joining'  => $doj,
                'status'           => $validated['status'],
                'email'            => $validated['official_email'],
                // Do not update password
            ]);
        }

        // Sync company assignments
         $user->companies()->sync($validated['companies']);

        if ($previousUserTypeId !== $validated['user_type_id']) {
            UserMenuPrivilege::where('user_id', $user->id)->delete();

            $userTypePrivileges = UserTypeMenuPrivilege::where('user_type_id', $validated['user_type_id'])->get();
            foreach ($userTypePrivileges as $typePriv) {
                UserMenuPrivilege::create([
                    'user_id' => $user->id,
                    'menu_id' => $typePriv->menu_id,
                    'can_menu' => $typePriv->can_menu,
                    'can_add' => $typePriv->can_add,
                    'can_edit' => $typePriv->can_edit,
                    'can_delete' => $typePriv->can_delete,
                    'can_view' => $typePriv->can_view,
                ]);
            }

            Log::info("✅ User privileges resynced for user: {$user->name} to user type ID: {$validated['user_type_id']}");
        }

        // ✅ If checkbox NOT TICKED → do not send email
if (!$request->has('send_email')) {
    return redirect()->route('users.index')->with('success', 'User updated successfully!');
}

// ✅ Load first company
$firstCompanyId = $validated['companies'][0];
$firstCompany = Company::find($firstCompanyId);

// ✅ Template Master
$template = EmailTemplate::where('company_id', $firstCompanyId)->first();

$useSystemDefault = false;

if (!$template) {
    $useSystemDefault = true;
    $template = (object)[
        'subject' => 'Your account has been updated at {company_name}',
        'body' => 'Hello {name},<br><br>Your account details have been updated successfully.<br><br>Email: {email}<br>Status: {status}<br><br>Regards,<br>Team {company_name}'
    ];
}

// ✅ Replace placeholders
$body = str_replace(
    ['{name}', '{company_name}', '{email}', '{status}'],
    [
        $user->name,
        $firstCompany->company_name,
        $user->official_email,
        $user->status
    ],
    $template->body
);

$subject = str_replace(
    ['{company_name}', '{name}'],
    [$firstCompany->company_name, $user->name],
    $template->subject
);

// ✅ Prepare email data
$emailData = [
    'name'    => $user->name,
    'company' => $firstCompany->company_name,
    'email'   => $user->official_email,
    'status'  => $user->status,
    'body'    => $body,
    'subject' => $subject,
    'mail_signature' => $companySetting->mail_signature 
                    ?? $firstCompany->mail_signature 
                    ?? null,
    'password'     => $password,
];

if (!$useSystemDefault) {
    $emailData['template_body'] = $body;
}

// ✅ Apply company mail config
$companySetting = CompanySetting::where('company_id', $firstCompanyId)->first()
            ?? CompanySetting::find(1);

if ($companySetting && $companySetting->mail_host) {
    // Priority 2 – settings table
    MailHelper::setMailConfig($firstCompanyId);
} else {
    // Priority 1 – company master config
    MailHelper::setMailConfig($firstCompanyId);
}

// ✅ Send email
try {
    Mail::to($user->official_email)->send(new CustomUserMail($emailData));
    Log::info("✅ Update email sent to {$user->official_email}");
} catch (\Exception $e) {
    Log::error("❌ Update email error: ".$e->getMessage());
}

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
     * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:users,id',
        ]);

        User::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('users.index')
            ->with('success', count($request->input('ids')) . ' user(s) deleted successfully.');
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