<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\MakeType;
use App\Models\DeliverablePlan;
use App\Models\ModelType;

class FeasibilityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $query = Feasibility::with(['company', 'client']);

        // Normal users: only feasibility records belonging to their assigned companies
        if (!in_array($user->user_type_id, [1, 2])) {
            $companyIds = $user->company()->pluck('companies.id');
            $query->whereIn('company_id', $companyIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->orWhere('type_of_service', 'like', "%{$search}%")
                  ->orWhere('delivery_company_name', 'like', "%{$search}%")
                  ->orWhere('circuit_id', 'like', "%{$search}%")
                  ->orWhere('location_id', 'like', "%{$search}%")
                  ->orWhere('longitude', 'like', "%{$search}%")
                  ->orWhere('latitude', 'like', "%{$search}%")
                  ->orWhere('pincode', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('spoc_name', 'like', "%{$search}%")
                  ->orWhere('spoc_contact1', 'like', "%{$search}%")
                  ->orWhere('spoc_contact2', 'like', "%{$search}%")
                  ->orWhere('spoc_email', 'like', "%{$search}%")
                  ->orWhere('no_of_links', 'like', "%{$search}%")
                  ->orWhere('speed', 'like', "%{$search}%")
                  ->orWhere('vendor_type', 'like', "%{$search}%")
                  ->orWhere('static_ip', 'like', "%{$search}%")
                  ->orWhere('static_ip_subnet', 'like', "%{$search}%")
                  ->orWhere('static_ip_duration', 'like', "%{$search}%")
                  ->orWhere('expected_delivery', 'like', "%{$search}%")
                  ->orWhere('expected_activation', 'like', "%{$search}%")
                  // Search by client name via relationship
                  ->orWhereHas('client', function($cq) use ($search) {
                      $cq->where('client_name', 'like', "%{$search}%");
                  });
            });
        }

        $feasibilities = $query->latest()->paginate($perPage);

        
        $permissions = TemplateHelper::getUserMenuPermissions('Feasibility Master') ?? (object)[
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
        // Get unique states from clients
        $clientStates = Client::select('state')->distinct()->pluck('state')->filter();

        $makes     = MakeType::all();
        $models    = ModelType::all();
        // Use DeliverablePlan for circuit_id dropdown
        $deliverables_plans = \App\Models\DeliverablePlan::all();
        return view('feasibility.create', compact('clients', 'companies', 'makes', 'models', 'deliverables_plans', 'clientStates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_of_service' => 'required',
            'company_id' => 'required|exists:companies,id',
            'client_state' => 'required|string',
            'client_id' => 'required',
            'link_type' => 'required|string',
            'delivery_company_name' => 'nullable|string',
            'circuit_id' => 'nullable|string',
            'location_id' => 'nullable|string',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'pincode' => 'required|digits:6',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'nullable',
            'spoc_contact1' => 'nullable|digits:10',
            'spoc_contact2' => 'nullable|digits:10',
            'spoc_email' => 'nullable|email',
            'no_of_links' => 'required|numeric|min:1',
            'speed' => 'required',
            'vendor_type' => 'required',
            'static_ip' => 'required|in:Yes,No',
            'static_ip_subnet' => $request->static_ip == 'Yes' ? 'required' : 'nullable',
            'static_ip_duration' => $request->static_ip == 'Yes' ? 'required|in:Monthly,Yearly' : 'nullable',
            'expected_delivery' => 'nullable|date',
            'expected_activation' => 'nullable|date',
            'hardware_required' => 'required|in:0,1',
            // 'hardware_model_name' => 'nullable',
            'hardware_make' => 'array|nullable',
            'hardware_model' => 'array|nullable',
            'existing_links' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

         // 🧠 Convert DD-MM-YYYY to YYYY-MM-DD before saving
    $validated['expected_delivery'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_delivery)));
    $validated['expected_activation'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_activation)));
    
    // 🧠 Convert hardware_required to proper boolean
    $validated['hardware_required'] = (bool) $validated['hardware_required'];
    
        // Add created_by
        $validated['created_by'] = Auth::user()->id;

        if ($validated['static_ip'] !== 'Yes') {
            $validated['static_ip_subnet'] = null;
            $validated['static_ip_duration'] = null;
        }
    // Build hardware JSON
   $hardwareData = [];

if ($request->hardware_required == '1' && $request->hardware_make && $request->hardware_model) {
    foreach ($request->hardware_make as $index => $make) {
        $hardwareData[] = [
            'make_type_id' => $make,
            'model_id' => $request->hardware_model[$index] ?? null,
        ];
    }
}

    // Store JSON in validated array
    $validated['hardware_details'] = json_encode($hardwareData);

        $feasibility = Feasibility::create($validated);
        $this->sendCreatedEmail($feasibility);


        // ⚙️ Automatically create feasibility status entry for operations
        FeasibilityStatus::create([
            'feasibility_id' => $feasibility->id,
            'status' => 'Open',
        ]);
// get status row
$status = FeasibilityStatus::where('feasibility_id', $feasibility->id)->first();

        // SELF vendor auto assign
$selfVendors = ['UBN', 'UBS', 'UBL', 'INF'];

if (in_array($validated['vendor_type'], $selfVendors)) {

    // SELF vendor
    $status->vendor1_name = 'Self';
    $status->vendor1_arc = $request->vendor1_arc;
    $status->vendor1_otc = $request->vendor1_otc;
    $status->vendor1_static_ip_cost = $request->vendor1_static_ip_cost;
    $status->vendor1_delivery_timeline = $request->vendor1_delivery_timeline;
    $status->vendor1_remarks = $request->vendor1_remarks;

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
 
public function sendCreatedEmail($feasibility)
{
    try {
        // Fetch template for feasibility creation
        $template = \App\Models\EmailTemplate::where('event_key', 'feasibility_create')->where('status', 'Active')->first();
        if ($template) {
            // Get recipient email directly from Company Settings
            $settings = \App\Models\CompanySetting::first();
            $notifyConfig = $settings->feasibility_notifications ?? [];
            $recipient = $notifyConfig['Open_email'] ?? null;
            Log::info('[Feasibility Email] Picked recipient', ['recipient' => $recipient]);
            if ($recipient) {
                $creator = $feasibility->createdByUser ? $feasibility->createdByUser->name : 'Unknown User';
                $body = str_replace(['{{feasibility_id}}',
                 '{{creator_name}}',
                 '{{company_name}}',
                 '{{client_name}}',
                 '{{address}}',
                 '{{speed}}',
                 '{{static_ip}}'
                 ], [$feasibility->feasibility_request_id, $creator, 
                 $feasibility->company->company_name ?? '',
                  $feasibility->client->client_name ?? '',
                  $feasibility->address ?? '',
                    $feasibility->speed ?? '',
                    $feasibility->static_ip ?? ''
                  ], $template->body);
                Log::info('[Feasibility Email] Triggering mail send', ['to' => $recipient, 'subject' => $template->subject]);
                Mail::send([], [], function ($message) use ($recipient, $template, $body) {
                    $message->to($recipient)
                        ->subject($template->subject)
                        ->html($body);
                });
            } else {
                Log::warning('[Feasibility Email] No recipient found in Company Settings.');
            }
        } else {
            Log::warning('[Feasibility Email] No active template found for feasibility_create.');
        }
    } catch (\Exception $e) {
        Log::error('Feasibility create email failed', [
            'error' => $e->getMessage()
        ]);
    }
}

     public function update(Request $request, $id)
    {
        Log::info('Feasibility update called', ['id' => $id, 'payload' => $request->all()]);

        $validated = $request->validate([
            'type_of_service' => 'required',
            'company_id' => 'required|exists:companies,id',
            'client_state' => 'required|string',
            'client_id' => 'required',
            'circuit_id' => 'nullable|string',
            'link_type' => 'required|string',
            'delivery_company_name' => 'nullable|string',
            'location_id' => 'nullable|string',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'pincode' => 'required',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'nullable',
            'spoc_contact1' => 'nullable|digits:10',
            'spoc_contact2' => 'nullable|digits:10',
            'spoc_email' => 'nullable|email',
            'no_of_links' => 'required',
            'speed' => 'required',
            'vendor_type' => 'required',
            'static_ip' => 'required|in:Yes,No',
            'static_ip_subnet' => $request->static_ip == 'Yes' ? 'required' : 'nullable',
            'static_ip_duration' => $request->static_ip == 'Yes' ? 'required|in:Monthly,Yearly' : 'nullable',
            'expected_delivery' => 'nullable|date',
            'expected_activation' => 'nullable|date',
            'hardware_required' => 'required|in:0,1',
            // 'hardware_model_name' => 'nullable',
            'hardware_make' => 'array|nullable',
            'hardware_model' => 'array|nullable',
            'existing_links' => 'nullable|numeric|min:0',
            // Status is stored but not edited here; allow any existing value
            'status' => 'nullable|string',
        ]);

         // 🧠 Convert DD-MM-YYYY to YYYY-MM-DD before saving
    $validated['expected_delivery'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_delivery)));
    $validated['expected_activation'] = date('Y-m-d', strtotime(str_replace('-', '/', $request->expected_activation)));
    
    // 🧠 Convert hardware_required to proper boolean
    $validated['hardware_required'] = (bool) $validated['hardware_required'];

        // Add updated_by
        $validated['updated_by'] = Auth::user()->id;

    // Build hardware JSON exactly like store()
    $hardwareData = [];
    if ($request->hardware_required == '1') {
        $hardwareMakes = $request->hardware_make ?? [];
        $hardwareModels = $request->hardware_model_name ?? [];
        for ($i = 0; $i < count($hardwareMakes); $i++) {
            if (empty($hardwareMakes[$i]) && empty($hardwareModels[$i])) {
                continue;
            }
            $hardwareData[] = [
                'make'  => $hardwareMakes[$i],
                'model' => $hardwareModels[$i],
            ];
        }
    }

    // Add JSON to validated array
    $validated['hardware_details'] = json_encode($hardwareData);

    if ($validated['static_ip'] !== 'Yes') {
        $validated['static_ip_subnet'] = null;
        $validated['static_ip_duration'] = null;
    }

    // find old record
    $feasibility = Feasibility::findOrFail($id);

    // update
    $feasibility->update($validated);

    Log::info('Feasibility updated successfully', ['id' => $feasibility->id]);
    // load status
$status = FeasibilityStatus::where('feasibility_id', $feasibility->id)->first();


    // SELF vendor auto assign
    $selfVendors = ['UBN', 'UBS', 'UBL', 'INF'];
    if (in_array($validated['vendor_type'], $selfVendors)) {
        $status->vendor1_name = 'Self';
        $status->vendor1_arc = $request->vendor1_arc;
        $status->vendor1_otc = $request->vendor1_otc;
        $status->vendor1_static_ip_cost = $request->vendor1_static_ip_cost;
        $status->vendor1_delivery_timeline = $request->vendor1_delivery_timeline;
        $status->vendor1_remarks = $request->vendor1_remarks;
        $status->vendor2_name = null;
        $status->vendor3_name = null;
        $status->vendor4_name = null;
        // Bypass any 'same vendor' exception requirement for Self vendors
        // (No validation block here, always allow close for Self)
    } else {
        // If not self vendor, you may keep your existing validation here if needed
        $status->vendor1_name = $request->vendor1_name;
        $status->vendor2_name = $request->vendor2_name;
        $status->vendor3_name = $request->vendor3_name;
        $status->vendor4_name = $request->vendor4_name;
        // (If you have a same-vendor validation, it would go here)
    }
    $status->save();

    // Debug: Log the status value
    Log::info('[Feasibility Update] Status value for close email check', [
        'status' => $validated['status'] ?? null,
        'feasibility_id' => $feasibility->id
    ]);

    // If status is being set to Closed in FeasibilityStatus, always sync and trigger email
    $isClosing = false;
    if (
        (isset($validated['status']) && strtolower(trim($validated['status'])) === 'closed') ||
        (isset($request->status) && strtolower(trim($request->status)) === 'closed') ||
        ($status && strtolower(trim($status->status)) === 'closed')
    ) {
        $isClosing = true;
        // Force main feasibility status to Closed
        if ($feasibility->status !== 'Closed') {
            $feasibility->status = 'Closed';
            $feasibility->save();
        }
        // Sync FeasibilityStatus as well
        if ($status && $status->status !== 'Closed') {
            $status->status = 'Closed';
            $status->save();
        }
    }
    if ($isClosing) {
        Log::info('[Feasibility Update] About to call sendCompletedEmail', [
            'feasibility_id' => $feasibility->id,
            'feasibility_status' => $feasibility->status,
            'status_table_status' => $status ? $status->status : null
        ]);
        $this->sendCompletedEmail($feasibility);
    }


        return redirect()->route('feasibility.index')->with('success', 'Feasibility added successfully!');
    }


    public function edit(Feasibility $feasibility)
    {
        $companies = Company::all();
        $clients = Client::all();
        $makes = MakeType::all();
        $models = ModelType::all();
        $deliverables_plans = \App\Models\DeliverablePlan::all();

        $clientStates = Client::select('state')->distinct()->pluck('state')->filter();


        return view('feasibility.edit', compact('feasibility', 'companies', 'clients', 'makes', 'models', 'deliverables_plans', 'clientStates'));
    }

//     public function show(Feasibility $feasibility)
// {
//     $feasibility->load('company', 'client'); // LOAD RELATIONS
//     return view('feasibility.view', compact('feasibility'));
// }

    /* ========================================================
        🔹 VIEW PAGE — RETURN VENDOR + CLIENT + FEASIBILITY PROPERLY
    ======================================================== */
    public function view($id)
    {
        $record = FeasibilityStatus::with([
            'feasibility.company', 
            'feasibility.client'
        ])->findOrFail($id);

        return view('sm.feasibility.view', compact('record'));
    }

    public function show(Feasibility $feasibility)
    {
        $feasibility->load(['company', 'client']);
        return view('feasibility.view', compact('feasibility'));
    }

public function sendCompletedEmail($feasibility)
{
    $template = \App\Models\EmailTemplate::where([
        ['event_key', '=', 'feasibility_completed'],
        ['status', '=', 'Active'],
    ])->first();

    if (!$template) return;

    $recipient =
        optional($feasibility->createdByUser)->official_email
        ?? optional($feasibility->createdByUser)->email;

    // if (!$recipient) return;

    // $map = [
    //     'feasibility_id' => $feasibility->feasibility_request_id ?? '',
    //     'company_name'   => optional($feasibility->company)->company_name ?? '',
    //     'client_name'    => optional($feasibility->client)->client_name ?? '',
    //     'address'        => $feasibility->address ?? '',
    //     'speed'          => $feasibility->speed ?? '',
    //     'static_ip'      => $feasibility->static_ip ?? '',
    //     'closed_by'      => optional($feasibility->updatedByUser)->name ?? '',
    //     'date'           => $feasibility->updated_at
    //                             ? $feasibility->updated_at->format('d-m-Y H:i')
    //                             : '',
    // ];

    // // 🔥 CRITICAL FIX
    // $body = TemplateHelper::renderTemplate(html_entity_decode($template->body), $map);

    if ($recipient) {
                $creator = $feasibility->createdByUser ? $feasibility->createdByUser->name : 'Unknown User';
                $body = str_replace(['{{feasibility_id}}',
                 '{{creator_name}}',
                 '{{company_name}}',
                 '{{client_name}}',
                 '{{address}}',
                 '{{speed}}',
                 '{{static_ip}}',
                 '{{closed_by}}',
                 '{{date}}'
                 ], [$feasibility->feasibility_request_id, $creator, 
                 $feasibility->company->company_name ?? '',
                  $feasibility->client->client_name ?? '',
                  $feasibility->address ?? '',
                    $feasibility->speed ?? '',
                    $feasibility->static_ip ?? '',
                    optional($feasibility->updatedByUser)->name ?? '',
                    $feasibility->updated_at ? $feasibility->updated_at->format('d-m-Y H:i') : ''
                  ], $template->body);
                Log::info('[Feasibility Email] Triggering mail send', ['to' => $recipient, 'subject' => $template->subject]);
                Mail::send([], [], function ($message) use ($recipient, $template, $body) {
                    $message->to($recipient)
                        ->subject($template->subject)
                        ->html($body);
                });
            }


    Mail::send([], [], function ($message) use ($recipient, $template, $body) {
        $message->to($recipient)
            ->subject($template->subject)
            ->html($body);
    });
    Log::info('✅ Completed mail sent with placeholders replaced');
}

    /**
     * AJAX: Fetch feasibility data by circuit_id (from deliverable_plan)
     */
    // public function fetchByCircuit($circuit_id)
    // {
    //    0 // Find deliverable plan by circuit_id
    //     $deliverable = \App\Models\DeliverablePlan::where('circuit_id', $circuit_id)->first();
    //     if (!$deliverable) {
    //         return response()->json(['success' => false, 'message' => 'No deliverable found.']);
    //     }
    //     // Traverse: DeliverablePlan -> Deliverables -> Feasibility
    //     Log::info('[fetchByCircuit] circuit_id: ' . $circuit_id);
    //     $feasibility = null;
    //     if (!empty($deliverable->deliverable_id)) {
    //         Log::info('[fetchByCircuit] deliverable_id: ' . $deliverable->deliverable_id);
    //         $deliverableRecord = \App\Models\Deliverables::where('id', $deliverable->deliverable_id)->first();
    //         Log::info('[fetchByCircuit] deliverableRecord: ', $deliverableRecord ? $deliverableRecord->toArray() : []);
    //         if ($deliverableRecord && !empty($deliverableRecord->feasibility_id)) {
    //             Log::info('[fetchByCircuit] deliverableRecord.feasibility_id: ' . $deliverableRecord->feasibility_id);
    //             $feasibility = \App\Models\Feasibility::where('id', $deliverableRecord->feasibility_id)->first();
    //         }
    //     }
    //     // Fallback: try by client_feasibility or client_id if above not found
    //     if (!$feasibility && !empty($deliverable->client_feasibility)) {
    //         Log::info('[fetchByCircuit] Trying client_feasibility: ' . $deliverable->client_feasibility);
    //         $feasibility = \App\Models\Feasibility::where('feasibility_request_id', $deliverable->client_feasibility)->first();
    //     }
    //     if (!$feasibility && !empty($deliverable->client_circuit_id)) {
    //         Log::info('[fetchByCircuit] Trying client_circuit_id: ' . $deliverable->client_circuit_id);
    //         $feasibility = \App\Models\Feasibility::where('client_id', $deliverable->client_circuit_id)
    //             ->orderByDesc('id')->first();
    //     }
    //     if (!$feasibility) {
    //         Log::warning('[fetchByCircuit] No feasibility found for circuit_id: ' . $circuit_id);
    //         return response()->json(['success' => false, 'message' => 'No feasibility found.']);
    //     }
    //     // Log::info('[fetchByCircuit] FeasibilgetFillableity found: ', $feasibility->toArray());
    //     // // Return all fillable fields for autofill
    //     // $fields = $feasibility->only($feasibility->getFillable());
    //     // return response()->json(['success' => true, 'feasibility' => $fields]);
    // }


    // 

    public function getFeasibilityByCircuit($circuit_id)
{
    $plan = DeliverablePlan::with([
        'deliverable.feasibility.client',
        'deliverable.feasibility.company',
    ])->where('circuit_id', $circuit_id)->first();

    if (!$plan || !$plan->deliverable || !$plan->deliverable->feasibility) {
        return response()->json([
            'success' => false,
            'message' => 'Feasibility not found'
        ]);
    }

    return response()->json([
        'success' => true,
        'feasibility' => $plan->deliverable->feasibility
    ]);
}

    
}
