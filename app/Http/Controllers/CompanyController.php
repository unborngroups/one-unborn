<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Company;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    public function store(Request $request)
    {
       $validated = $request->validate([
    'trade_name' => 'nullable|string|max:255',
    'company_name' => 'required|string|max:255',
    'business_number' => 'nullable|string|max:255',
    'company_phone' => 'nullable|string|max:20',
    'company_email' => 'nullable|email|max:255',
    // 'secondary_email' => 'nullable|email|max:255',
    'alternative_contact_number' => 'nullable|string|max:20',
    'website' => 'nullable|string|max:255',
    'gstin' => 'nullable|string|max:15',
    'pan_number' => 'nullable|string|max:10',
    'address' => 'nullable|string',
    'billing_logo' => 'nullable|image|max:2048',
    'billing_sign_normal' => 'nullable|image|max:2048',
    'billing_sign_digital' => 'nullable|image|max:2048',
    'branch_location' => 'nullable|string|max:255',
    'store_location_url' => 'nullable|string|max:255',
    'google_place_id' => 'nullable|string|max:255',
    'instagram' => 'nullable|string|max:255',
    'youtube' => 'nullable|string|max:255',
    'facebook' => 'nullable|string|max:255',
    'linkedin' => 'nullable|string|max:255',
    'account_number' => 'nullable|string|max:50',
    'ifsc_code' => 'nullable|string|max:11',
    'branch_name' => 'nullable|string|max:255',
    'bank_name' => 'nullable|string|max:255',
    'upi_id' => 'nullable|string|max:255',
    'upi_number' => 'nullable|string|max:20',
    'opening_balance' => 'nullable|numeric',
     'status'           => 'required|in:Active,Inactive',
]);

// ✅ Upload images to public/images/...
        $validated['billing_logo'] = $this->uploadImage($request, 'billing_logo', 'logos');
        $validated['billing_sign_normal'] = $this->uploadImage($request, 'billing_sign_normal', 'n_signs');
        $validated['billing_sign_digital'] = $this->uploadImage($request, 'billing_sign_digital', 'd_signs');

// ✅ Insert into database
    Company::create($validated);

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
        try {
            $validated = $request->validate([
                'trade_name' => 'nullable|string|max:255',
                'company_name' => 'required|string|max:255',
                'business_number' => 'nullable|string|max:255',
                'company_phone' => 'nullable|string|max:20',
                'company_email' => 'nullable|email|max:255',
                'alternative_contact_number' => 'nullable|string|max:20',
                'website' => 'nullable|string|max:255',
                'gstin' => 'nullable|string|max:15',
                'pan_number' => 'nullable|string|max:10',
                'address' => 'nullable|string',
                'billing_logo' => 'nullable|image|max:2048',
                'billing_sign_normal' => 'nullable|image|max:2048',
                'billing_sign_digital' => 'nullable|image|max:2048',
                'branch_location' => 'nullable|string|max:255',
                'store_location_url' => 'nullable|string|max:255',
                'google_place_id' => 'nullable|string|max:255',
                'instagram' => 'nullable|string|max:255',
                'youtube' => 'nullable|string|max:255',
                'facebook' => 'nullable|string|max:255',
                'linkedin' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:11',
                'branch_name' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'upi_id' => 'nullable|string|max:255',
                'upi_number' => 'nullable|string|max:20',
                'opening_balance' => 'nullable|numeric',
                'status' => 'required|in:Active,Inactive',
            ]);

            // Log for debugging
            Log::info('Company Update - ID: ' . $company->id . ', Data: ' . json_encode($validated));
            
            // ✅ Replace images if new uploaded
        $validated['billing_logo'] = $this->updateImage($request, $company->billing_logo, 'billing_logo', 'logos');
        $validated['billing_sign_normal'] = $this->updateImage($request, $company->billing_sign_normal, 'billing_sign_normal', 'n_signs');
        $validated['billing_sign_digital'] = $this->updateImage($request, $company->billing_sign_digital, 'billing_sign_digital', 'd_signs');

            // Update the existing company
            $company->update($validated);
            
            Log::info('Company Updated Successfully - ID: ' . $company->id);
            
            return redirect()->route('companies.index')->with('success', 'Company updated successfully!');
            
        } catch (\Exception $e) {
            Log::error('Company Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating company: ' . $e->getMessage())->withInput();
        }
    }
        /**
        * Remove the specified resource from storage.
        */
    public function destroy($id)
{
    try {
        $company = Company::findOrFail($id);
        
        // Delete images before deleting company record
        $this->deleteImage('images/logos/' . $company->billing_logo);
        $this->deleteImage('images/n_signs/' . $company->billing_sign_normal);
        $this->deleteImage('images/d_signs/' . $company->billing_sign_digital);

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    } catch (\Exception $e) {
        Log::error('Error deleting company: '.$e->getMessage());
        return redirect()->route('companies.index')
            ->with('error', 'Failed to delete company.');
    }
}


    // Active or Inactive button

 public function toggleStatus($id)
{
    $company = Company::findOrFail($id);
    // Toggle Active/Inactive
    $company->status = $company->status === 'Active' ? 'Inactive' : 'Active';
    $company->save();

    return redirect()->route('companies.index')
                     ->with('success', 'Company status updated successfully.');
}

    public function templates($id)
 {
    $company = Company::findOrFail($id);
    $templates = $company->templates()->select('id', 'subject')->get();


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
    CompanySetting::updateOrCreate(
    ['company_id' => $company->id],
    $data
);

    return redirect()->route('companies.index')->with('success', 'Email configuration saved successfully.');
}
    
  public function fetchByPan($pan)
{
    try {
        // Log the incoming PAN for debugging
        Log::info("PAN Fetch Request: {$pan}");
        
        // ✅ Clean and validate PAN format
        $cleanPan = strtoupper(trim($pan));
        
        if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $cleanPan)) {
            Log::warning("Invalid PAN format: {$pan}");
            return response()->json(['success' => false, 'message' => 'Invalid PAN format. Should be 10 characters (e.g., AABCI9011R)']);
        }

        // Search for company with exact PAN match
        $company = Company::where('pan_number', $cleanPan)->first();
        
        Log::info("PAN search result: " . ($company ? "Found company ID {$company->id}" : "No company found"));

        if ($company) {
            return response()->json([
                'success' => true,
                'data' => [
                    'company_name' => $company->company_name,

                    'gstin'       => $company->gstin,
                    'company_email'=> $company->company_email ?? '',
                    'address'      => $company->address ?? '',
                    'trade_name'   => $company->trade_name ?? '',
                    'company_phone'=> $company->company_phone ?? '',
                ],
                'message' => 'Company details found and auto-filled!'
            ]);
        }

        // If not found, let's check if there are similar PAN numbers
        $similarCompanies = Company::where('pan_number', 'LIKE', "%{$cleanPan}%")->get(['pan_number', 'company_name']);
        Log::info("Similar companies found: " . $similarCompanies->count());

        return response()->json([
            'success' => false, 
            'message' => 'No company found with PAN: ' . $cleanPan,
            'suggestion' => $similarCompanies->count() > 0 ? 'Similar PANs found: ' . $similarCompanies->pluck('pan_number')->implode(', ') : null
        ]);
        
    } catch (\Exception $e) {
        Log::error("PAN Fetch Error: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error fetching company details']);
    }
}
// 
public function fetchGst($gst)
{
    try {
        // ✅ Validate GST number format
        if (!preg_match("/^[0-9A-Z]{15}$/", $gst)) {
            return response()->json(['error' => 'Invalid GST number format'], 400);
        }

        Log::info("Fetching GST data for: {$gst}");

        // ✅ Try fetching from GSTINCheck (Free API)
        $response = Http::timeout(10)->get("https://sheet.gstincheck.co.in/check/{$gst}");

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['flag']) && $data['flag'] === true && isset($data['data'])) {
                Log::info('✅ GST fetched successfully from GSTINCheck');

                $gstData = [
                    'trade_name' => $data['data']['tradeNam'] ?? '',
                    'legal_name' => $data['data']['lgnm'] ?? '',
                    'pradr' => [
                        'addr' => [
                            'bno'  => $data['data']['pradr']['addr']['bno'] ?? '',
                            'st'   => $data['data']['pradr']['addr']['st'] ?? '',
                            'loc'  => $data['data']['pradr']['addr']['loc'] ?? '',
                            'dst'  => $data['data']['pradr']['addr']['dst'] ?? '',
                            'stcd' => $data['data']['pradr']['addr']['stcd'] ?? '',
                            'pncd' => $data['data']['pradr']['addr']['pncd'] ?? '',
                        ]
                    ],
                    'status' => $data['data']['sts'] ?? '',
                    'source' => 'gstincheck'
                ];

                // ✅ Build full address string for auto-fill
                $gstData['address'] = implode(', ', array_filter([
                    $gstData['pradr']['addr']['bno'] ?? '',
                    $gstData['pradr']['addr']['st'] ?? '',
                    $gstData['pradr']['addr']['loc'] ?? '',
                    $gstData['pradr']['addr']['dst'] ?? '',
                    $gstData['pradr']['addr']['stcd'] ?? '',
                    $gstData['pradr']['addr']['pncd'] ?? ''
                ]));

                return response()->json($gstData);
            }
        }

        Log::warning("❌ GSTINCheck did not return valid data for {$gst}");

        return response()->json([
            'error' => 'GST details not found or unavailable. Please fill manually.',
            'gst_number' => $gst
        ], 404);

    } catch (\Exception $e) {
        Log::error('GST Fetch Error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Error fetching GST details. Please enter manually.',
            'details' => $e->getMessage()
        ], 500);
    }
}


private function fetchFromRealAPIs($gst)
{
    // API 1: API Ninjas (Your paid API)
    $result = $this->tryApiNinjas($gst);
    if ($result) return $result;

    // API 2: GST India Check (Free backup)
    $result = $this->tryGstIndiaCheck($gst);
    if ($result) return $result;

    // API 3: Another free GST API
    $result = $this->tryMasterGST($gst);
    if ($result) return $result;

    return null;
}

private function tryApiNinjas($gst)
{
    try {
        // Skip API Ninjas for now as it doesn't support GST endpoint
        Log::info('Skipping API Ninjas - GST endpoint not available');
        return null;
    } catch (\Exception $e) {
        Log::warning('❌ API Ninjas GST fetch failed: ' . $e->getMessage());
    }
    
    return null;
}

private function tryGstIndiaCheck($gst)
{
    try {
        $response = Http::timeout(10)->get("https://sheet.gstincheck.co.in/check/{$gst}");
        
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['flag']) && $data['flag'] === true && isset($data['data'])) {
                Log::info('✅ GST fetched from GST India Check successfully');
                
                return [
                    'trade_name' => $data['data']['tradeNam'] ?? '',
                    'legal_name' => $data['data']['lgnm'] ?? '',
                    'pradr' => [
                        'addr' => [
                            'bno' => $data['data']['pradr']['addr']['bno'] ?? '',
                            'st' => $data['data']['pradr']['addr']['st'] ?? '',
                            'loc' => $data['data']['pradr']['addr']['loc'] ?? '',
                            'dst' => $data['data']['pradr']['addr']['dst'] ?? '',
                            'stcd' => $data['data']['pradr']['addr']['stcd'] ?? '',
                            'pncd' => $data['data']['pradr']['addr']['pncd'] ?? ''
                        ]
                    ],
                    'status' => $data['data']['sts'] ?? '',
                    'source' => 'gstincheck'
                ];
            }
        }
    } catch (\Exception $e) {
        Log::warning('❌ GST India Check API failed: ' . $e->getMessage());
    }
    
    return null;
}


private function tryMasterGST($gst)
{
    try {
        // Use a simple GST verify API
        $response = Http::timeout(10)->get("https://gst-verify.com/api/verify/{$gst}");
        
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['success']) && $data['success'] === true) {
                Log::info('✅ GST fetched from GST Verify successfully');
                
                return [
                    'trade_name' => $data['data']['trade_name'] ?? '',
                    'legal_name' => $data['data']['legal_name'] ?? '',
                    'pradr' => [
                        'addr' => [
                            'bno' => $data['data']['address']['building'] ?? '',
                            'st' => $data['data']['address']['street'] ?? '',
                            'loc' => $data['data']['address']['location'] ?? '',
                            'dst' => $data['data']['address']['district'] ?? '',
                            'stcd' => $data['data']['address']['state'] ?? '',
                            'pncd' => $data['data']['address']['pincode'] ?? ''
                        ]
                    ],
                    'status' => $data['data']['status'] ?? '',
                    'source' => 'gst-verify'
                ];
            }
        }
    } catch (\Exception $e) {
        Log::warning('❌ GST Verify API failed: ' . $e->getMessage());
    }
    
    return null;
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
        if ($path && file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }

}