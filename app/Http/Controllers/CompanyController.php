<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Company;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
     /**
     * Display a listing of the resource.
     */
public function index()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $role = strtolower($user->userType->name ?? '');

    // 🔹 Superadmin/Admin — show all companies
    if (in_array($role, ['superadmin', 'admin'])) {
        $companies = Company::latest()->paginate(10);
    } else {
        // 🔹 Normal user — show only assigned companies
        $companies = $user->companies()->latest()->paginate(10);
    }

    // ✅ Get permissions safely
    $permissions = TemplateHelper::getUserMenuPermissions('Company Details') ?? (object)[
       
        'can_add' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_view' => true,
    ];

    return view('companies.index', compact('companies', 'permissions'));
}


    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email_1' => 'nullable|email',
            'email_2' => 'nullable|email',
            'billing_logo' => 'nullable|image|max:2048',
            'billing_sign_normal' => 'nullable|image|max:2048',
            'billing_sign_digital' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['billing_logo', 'billing_sign_normal', 'billing_sign_digital']);
// ✅ Upload images to public/images/...
        $data['billing_logo'] = $this->uploadImage($request, 'billing_logo', 'logos');
        $data['billing_sign_normal'] = $this->uploadImage($request, 'billing_sign_normal', 'n_signs');
        $data['billing_sign_digital'] = $this->uploadImage($request, 'billing_sign_digital', 'd_signs');


        Company::create($data);

        return redirect()->route('companies.index')->with('success', 'Company created successfully!');
    }
    //view 
    public function view($id)
{
    $company = \App\Models\Company::findOrFail($id);
    return view('companies.view', compact('company'));
}


    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email_1' => 'nullable|email',
            'email_2' => 'nullable|email',
            'billing_logo' => 'nullable|image|max:2048',
            'billing_sign_normal' => 'nullable|image|max:2048',
            'billing_sign_digital' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['billing_logo', 'billing_sign_normal', 'billing_sign_digital']);

    // ✅ Replace images if new uploaded
        $data['billing_logo'] = $this->updateImage($request, $company->billing_logo, 'billing_logo', 'logos');
        $data['billing_sign_normal'] = $this->updateImage($request, $company->billing_sign_normal, 'billing_sign_normal', 'n_signs');
        $data['billing_sign_digital'] = $this->updateImage($request, $company->billing_sign_digital, 'billing_sign_digital', 'd_signs');

        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully!');
    }
    
    public function destroy(Company $company)
    {
        $this->deleteImage('images/logos/' . $company->billing_logo);
        $this->deleteImage('images/signs/' . $company->billing_sign_normal);
        $this->deleteImage('images/signs/' . $company->billing_sign_digital);

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully!');
    }

    // Active or Inactive button

 public function toggleStatus($id)
{
    $company = Company::findOrFail($id);
    // Toggle Active/Inactive
    $company->status = $company->status === 'Active' ? 'Inactive' : 'Active';
    $company->save();

    return redirect()->route('companies.index')
                     ->with('success', 'company status updated successfully.');
}

    public function templates($id)
 {
    $templates = \App\Models\EmailTemplate::where('company_id', $id)
        ->select('id', 'subject')
        ->get();

    return response()->json($templates);
}
public function emailConfig($id)
{
    $company = Company::findOrFail($id);
    $setting = CompanySetting::where('company_id', $id)->first();

    return view('companies.email_config', compact('company', 'setting'));
}

public function saveEmailConfig(Request $request, $id)
{
     $company = Company::findOrFail($id);

    $validated = $request->validate([
        'mail_mailer' => 'required|string',
        'mail_host' => 'required|string',
        'mail_port' => 'required|numeric',
        'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            'is_default' => 'nullable|boolean',
    ]);

     // Ensure company_id exists in record
        $data = $validated;
        $data['company_id'] = $id;

        // If user checked is_default, unset is_default for others (only one default allowed)
        if (!empty($validated['is_default'])) {
            CompanySetting::query()->update(['is_default' => 0]);
            $data['is_default'] = 1;
        }
    // Save or update in company_settings table
    \App\Models\CompanySetting::updateOrCreate(
        ['company_id' => $company->id],
        $validated
    );

    return redirect()->route('companies.index')->with('success', 'Email configuration saved successfully.');
}

 // 🔧 Helper methods
    private function uploadImage($request, $field, $folder)
    {
        if ($request->hasFile($field)) {
            $file = $request->file($field);
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("images/{$folder}"), $filename);
            return $filename;
        }
        return null;
    }

    private function updateImage($request, $oldFile, $field, $folder)
    {
        if ($request->hasFile($field)) {
            $oldPath = public_path("images/{$folder}/{$oldFile}");
            if ($oldFile && file_exists($oldPath)) {
                unlink($oldPath);
            }

            $file = $request->file($field);
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("images/{$folder}"), $filename);
            return $filename;
        }
        return $oldFile;
    }

    private function deleteImage($path)
    {
         $fullPath = public_path($path);
        if ($path && file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}