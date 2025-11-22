<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use Illuminate\Support\Facades\Auth;

class FeasibilityController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ Superadmin (1) & Admin (2) can see all feasibility records
        if (in_array($user->user_type_id, [1, 2])) {
            $feasibilities = Feasibility::with(['company', 'client'])->latest()->paginate(10);
        } 
        else {
            // ✅ Normal users: only feasibility records belonging to their assigned companies
            $companyIds = $user->companies()->pluck('companies.id');

            $feasibilities = Feasibility::with(['company', 'client'])->whereIn('company_id', $companyIds)->latest()->paginate(10);
        }
    

        // Use the helper correctly
        $permissions = TemplateHelper::getUserMenuPermissions('Feasibility') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        return view('feasibility.index', compact('feasibilities', 'permissions'));
    }
   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Check if user is super admin
        if ($user->is_superuser || $user->user_type_id == 1) {
            // Super admin can see all companies
            $companies = Company::all();
        } else {
            // Regular users see only their assigned companies
            $companies = $user->companies;
        }
        
        // Clients are always independent - show all clients to everyone
        $clients = Client::all();
        
        return view('feasibility.create', compact('clients', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_of_service' => 'required',
            'company_id' => 'required|exists:companies,id',
            'client_id' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'required',
            'spoc_contact1' => 'required',
            'spoc_contact2' => 'nullable',
            'spoc_email' => 'nullable|email',
            'no_of_links' => 'required',
            'speed' => 'required',
            'vendor_type' => 'required',
            'static_ip' => 'required',
            'static_ip_subnet' => 'nullable',
            'expected_delivery' => 'required|date',
            'expected_activation' => 'required|date',
            'hardware_required' => 'required|in:0,1',
            'hardware_model_name' => 'nullable',
            'status' => 'required|in:Active,Inactive',

            'static_ip' => $request->type_of_service == 'ILL' ? 'required|in:Yes' : 'required',
    'static_ip_subnet' => $request->static_ip == 'Yes' ? 'required' : 'nullable',
        ]);

         // 🧠 Convert DD-MM-YYYY to YYYY-MM-DD before saving
    $validated['expected_delivery'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_delivery)));
    $validated['expected_activation'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_activation)));
    
    // 🧠 Convert hardware_required to proper boolean
    $validated['hardware_required'] = (bool) $validated['hardware_required'];
    
    // Add created_by
    $validated['created_by'] = Auth::user()->id;

        $feasibility = Feasibility::create($validated);

        // ⚙️ Automatically create feasibility status entry for operations
        FeasibilityStatus::create([
            'feasibility_id' => $feasibility->id,
            'status' => 'Open',
        ]);
// get status row
$status = FeasibilityStatus::where('feasibility_id', $feasibility->id)->first();

        // SELF vendor auto assign
$selfVendors = ['UNB', 'UNS', 'UBL', 'INF'];

if (in_array($validated['vendor_type'], $selfVendors)) {

    // SELF vendor
    $status->vendor1_name = 'Self';
    $status->vendor1_arc = $request->vendor1_arc;
    $status->vendor1_otc = $request->vendor1_otc;
    $status->vendor1_static_ip_cost = $request->vendor1_static_ip_cost;
    $status->vendor1_delivery_timeline = $request->vendor1_delivery_timeline;

    // clear others
    $status->vendor2_name = null;
    $status->vendor3_name = null;
    $status->vendor4_name = null;

} else {

    // Normal Vendors
    $status->vendor1_name = $request->vendor1_name;
    $status->vendor2_name = $request->vendor2_name;
    $status->vendor3_name = $request->vendor3_name;
    $status->vendor4_name = $request->vendor4_name;
}
$status->save();

        return redirect()->route('sm.feasibility.open')->with('success', 'Feasibility added successfully!');
    }

     public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type_of_service' => 'required',
            'company_id' => 'required|exists:companies,id',
            'client_id' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'required',
            'spoc_contact1' => 'required',
            'spoc_contact2' => 'nullable',
            'spoc_email' => 'nullable|email',
            'no_of_links' => 'required',
            'speed' => 'required',
            'vendor_type' => 'required',
            'static_ip' => 'required',
            'static_ip_subnet' => 'nullable',
            'expected_delivery' => 'required|date',
            'expected_activation' => 'required|date',
            'hardware_required' => 'required|in:0,1',
            'hardware_model_name' => 'nullable',
            'status' => 'required|in:Active,Inactive',
        ]);

         // 🧠 Convert DD-MM-YYYY to YYYY-MM-DD before saving
    $validated['expected_delivery'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_delivery)));
    $validated['expected_activation'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_activation)));
    
    // 🧠 Convert hardware_required to proper boolean
    $validated['hardware_required'] = (bool) $validated['hardware_required'];
    
   // find old record
    $feasibility = Feasibility::findOrFail($id);

    // update
    $feasibility->update($validated);
    // load status
$status = FeasibilityStatus::where('feasibility_id', $feasibility->id)->first();

    // SELF vendor auto assign
$selfVendors = ['UNB', 'UNS', 'UBL', 'INF'];

if (in_array($validated['vendor_type'], $selfVendors)) {

    $status->vendor1_name = 'Self';
    $status->vendor1_arc = $request->vendor1_arc;
    $status->vendor1_otc = $request->vendor1_otc;
    $status->vendor1_static_ip_cost = $request->vendor1_static_ip_cost;
    $status->vendor1_delivery_timeline = $request->vendor1_delivery_timeline;

    $status->vendor2_name = null;
    $status->vendor3_name = null;
    $status->vendor4_name = null;

} else {

    $status->vendor1_name = $request->vendor1_name;
    $status->vendor2_name = $request->vendor2_name;
    $status->vendor3_name = $request->vendor3_name;
    $status->vendor4_name = $request->vendor4_name;
}
$status->save();


        return redirect()->route('feasibility.index')->with('success', 'Feasibility added successfully!');
    }


    public function edit(Feasibility $feasibility)
    {
         $companies = Company::all();
    $clients = Client::all();
        return view('feasibility.edit', compact('feasibility', 'companies', 'clients'));
    }

    public function show(Feasibility $feasibility)
{
    $feasibility->load('company', 'client'); // LOAD RELATIONS
    return view('feasibility.view', compact('feasibility'));
}



}
