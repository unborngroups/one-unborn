<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TemplateHelper;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Company;
use App\Models\Gstin;
use App\Services\SurepassService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $permissions = TemplateHelper::getUserMenuPermissions('Client Master') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        // $query = Client::orderBy('id', 'desc');
        // $canViewAllClients = $permissions->can_view;

        // if (!$canViewAllClients) {
        //     $companyIds = $user->companies()->pluck('companies.id')->toArray();
        //     if (!empty($companyIds)) {
        //         $query->whereIn('company_id', $companyIds);
        //     }
        // }

        // Search filter
        // if ($request->filled('search')) {
        //     $search = $request->search;
        //     $query->where(function($q) use ($search) {
        //         $q->where('client_code', 'like', "%$search%")
        //           ->orWhere('client_name', 'like', "%$search%")
        //           ->orWhere('business_display_name', 'like', "%$search%")
        //           ->orWhere('support_spoc_name', 'like', "%$search%")
        //           ->orWhere('support_spoc_email', 'like', "%$search%")
        //           ->orWhere('support_spoc_mobile', 'like', "%$search%")
        //           ->orWhere('status', 'like', "%$search%")
        //         ;
        //     });
        // }

        $perPage = $request->input('per_page', 10); // default 10
        $perPage = in_array($perPage, [10,25,50,100]) ? $perPage : 10;
        $clients = Client::orderBy('id', 'desc')->paginate($perPage);
        return view('clients.index', compact('clients', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
 
    public function create()
{
    // Fetch all clients that are head offices
    $headOffices = Client::where('office_type', 'head')
                        ->orderBy('client_name')
                        ->get();

    // Pass it to the view
    return view('clients.create', compact('headOffices'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validated =  $request->validate([
        'pan_number' => 'nullable|string|size:10',
        'user_name' => $request->office_type === 'head'
            ? 'required|string|max:255'
            : 'nullable|string|max:255',

        'pan_number' => $request->office_type === 'head'
            ? 'required|string|size:10'
            : 'nullable|string|size:10',

            'client_name'          => 'required|string|max:255',
            'short_name'           => 'nullable|string|max:255',
            'client_code'          => 'nullable|string|max:50',
            'business_display_name'=> 'nullable|string|max:255',
            'office_type'          => 'required|in:head,branch',
            'head_office_id' => $request->office_type === 'branch'
    ? 'required|exists:clients,id'
    : 'nullable',

            'address1'             => 'nullable|string|max:255',
            'address2'             => 'nullable|string|max:255',
            'address3'             => 'nullable|string|max:255',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'country'              => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',

            // Business Contact
            'billing_spoc_name'    => 'nullable|string|max:255',
            'billing_spoc_contact' => 'nullable|string|max:20',
            'billing_spoc_email'   => 'nullable|email|max:255',
            'gstin'                => 'nullable|string|max:20',

            //New Invoice Emails
            'invoice_email'        => 'nullable|email|max:255',
            'invoice_cc'           => 'nullable|string|max:500',  // Multiple emails with semicolon

            // Technical Support
            'support_spoc_name'    => 'nullable|string|max:255',
            'support_spoc_mobile'  => 'nullable|string|max:20',
            'support_spoc_email'   => 'nullable|email|max:255',

            // Client Status
            'portal_password' => 'nullable|string|max:255',
             
            'status'           => 'required|in:Active,Inactive',
        ]);
// ⭐ CLIENT CODE LOGIC
   // ⭐ CLIENT CODE LOGIC (FINAL & CORRECT)

if ($request->office_type === 'head') {

    // Generate NEW client code only for Head Office
    $lastClient = Client::whereNotNull('client_code')
                        ->orderBy('id', 'desc')
                        ->first();

    $nextNumber = $lastClient
        ? ((int) substr($lastClient->client_code, 2)) + 1
        : 1;

    $validated['client_code'] = 'CL' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    $validated['head_office_id'] = null;

} else { 
    // Branch → inherit Head Office client_code
    $headOffice = Client::findOrFail($request->head_office_id);

    $validated['client_code'] = $headOffice->client_code;
    $validated['head_office_id'] = $headOffice->id;
}



        // ✅ Auto-generate client_code if not provided
    // if (empty($validated['client_code'])) {
    //     $lastClient = Client::latest('id')->first();
    //     $validated['client_code'] = 'CL' . str_pad(($lastClient->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    // }
    
// ⭐ Save portal credentials
$validated['user_name'] = $request->user_name;
$validated['portal_password'] = bcrypt($request->portal_password);
$validated['portal_active'] = 1;
    $client = Client::create($validated);
    // if ($request->ajax()) {
    //     return response()->json(['success' => true, 'client' => $client]);
    // }
    return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function view($id)
{
    $client = \App\Models\Client::findOrFail($id);
    return view('clients.view', compact('client'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
         // Fetch all clients that are head offices
    $headOffices = Client::where('office_type', 'head')
                        ->orderBy('client_name')
                        ->get();
                        
        return view('clients.edit', compact('client', 'headOffices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'pan_number' => 'nullable|string|size:10',
        'user_name' => $request->office_type === 'head'
            ? 'required|string|max:255'
            : 'nullable|string|max:255',

        'pan_number' => $request->office_type === 'head'
            ? 'required|string|size:10'
            : 'nullable|string|size:10',

        'client_name'          => 'required|string|max:255',
        'short_name'           => 'nullable|string|max:255',
        'client_code'          => 'nullable|string|max:50',
        'business_display_name'=> 'nullable|string|max:255',
        'office_type'          => 'required|in:head,branch',
        'head_office_id' => $request->office_type === 'branch'
    ? 'required|exists:clients,id'
    : 'nullable',
        'address1'             => 'nullable|string|max:255',
        'address2'             => 'nullable|string|max:255',
        'address3'             => 'nullable|string|max:255',
        'city'                 => 'nullable|string|max:100',
        'state'                => 'nullable|string|max:100',
        'country'              => 'nullable|string|max:100',
        'pincode'              => 'nullable|string|max:10',

         // Business Contact
        'billing_spoc_name'    => 'nullable|string|max:255',
        'billing_spoc_contact' => 'nullable|string|max:20',
        'billing_spoc_email'   => 'nullable|email|max:255',
        'gstin'                => 'nullable|string|max:20',

        // Invoice
        'invoice_email'        => 'nullable|email|max:255',
        'invoice_cc'           => 'nullable|string|max:500',  // Multiple emails with semicolon

        // Technical Support
        'support_spoc_name'    => 'nullable|string|max:255',
        'support_spoc_mobile'  => 'nullable|string|max:20',
        'support_spoc_email'   => 'nullable|email|max:255',

        'portal_password'      => 'nullable|string|max:255',
        'status'               => 'required|in:Active,Inactive',
        ]);
             
        // ⭐ CLIENT CODE LOGIC
    // ⭐ CLIENT CODE LOGIC (FINAL & CORRECT)

if ($request->office_type === 'head') {

    // Generate NEW client code only for Head Office
    $lastClient = Client::whereNotNull('client_code')
                        ->orderBy('id', 'desc')
                        ->first();

    $nextNumber = $lastClient
        ? ((int) substr($lastClient->client_code, 2)) + 1
        : 1;

    $validated['client_code'] = 'CL' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    $validated['head_office_id'] = null;

} else { 
    // Branch → inherit Head Office client_code
    $headOffice = Client::findOrFail($request->head_office_id);

    $validated['client_code'] = $headOffice->client_code;
    $validated['head_office_id'] = $headOffice->id;
}


       
    // Update fields
    $client->fill($validated);

    // Hash only when new password entered
    if ($request->portal_password) {
        $client->portal_password = bcrypt($request->portal_password);
    }

    $client->save();
        // if ($request->ajax()) {
        //     return response()->json(['success' => true, 'client' => $client]);
        // }
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        // if (request()->ajax()) {
        //     return response()->json(['success' => true]);
        // }
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    /**
     * Bulk delete clients selected from the index table.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:clients,id',
        ]);

        Client::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('clients.index')
            ->with('success', count($request->input('ids')) . ' client(s) deleted successfully.');
    }

    /**
     * 
     */
    public function toggleStatus($id)
{
    $client = Client::findOrFail($id);

    // Toggle Active/Inactive
    $client->status = $client->status === 'Active' ? 'Inactive' : 'Active';
    $client->save();

    return redirect()->route('clients.index')
                     ->with('success', 'Client status updated successfully.');
}
// Get Client Details for Feasibility
public function getDetails($id)
{
    $client = Client::find($id);
    return response()->json($client);
}

// PAN Verification
public function verifyPan(Request $request)
{
    $company = Company::where('pan_no', $request->pan)->first();
    if ($company) {
        return response()->json(['success' => true, 'data' => $company]);
    } else {
        return response()->json(['success' => false, 'message' => 'PAN not found']);
    }
}
// Fetch GST Details
public function fetchGST($pan, $state)
{
    $pan = strtoupper($pan);
    if (strlen($pan) !== 10) {
        return response()->json(['success' => false, 'message' => 'Invalid PAN']);
    }

    // Step 1: Generate GSTIN without checksum
    $partialGSTIN = $state . $pan . "1Z";

    // Step 2: Generate checksum
    $checksum = $this->getGSTChecksum($partialGSTIN);

    // Final GSTIN
    $gstin = $partialGSTIN . $checksum;

    // Step 3: Call GST API
    $url = "https://sheet.gstincheck.co.in/check/{$gstin}";
    $response = Http::timeout(10)->get($url);

    if ($response->failed() || !isset($response['tradeNam'])) {
        return response()->json(['success' => false]);
    }

    $data = [
        'gstin' => $gstin,
        'trade_name' => $response['tradeNam'],
        'address' => $response['pradr']['addr']['bno']
                    . ", " . $response['pradr']['addr']['st']
                    . ", " . $response['pradr']['addr']['dst'],
        'company_email' => $response['pradr']['email'] ?? '',
        'company_phone' => $response['pradr']['phone'] ?? '',
    ];

    return response()->json(['success' => true, 'data' => $data]);
}

/* Generate GSTIN Checksum */
private function getGSTChecksum($input)
{
    $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $factor = 1;
    $sum = 0;

    for ($i = 0; $i < strlen($input); $i++) {
        $codePoint = strpos($chars, $input[$i]);
        $digit = $factor * $codePoint;

        $factor = ($factor == 1) ? 2 : 1;

        $digit = floor($digit / 36) + ($digit % 36);
        $sum += $digit;
    }

    $checksumPoint = (36 - ($sum % 36)) % 36;
    return $chars[$checksumPoint];
}

/**
 * Fetch GSTIN by PAN using Surepass API
 */
public function fetchGstinByPan(Request $request)
{
    $request->validate([
        'pan_number' => 'required|string|size:10',
        'client_id' => 'nullable|integer'
    ]);

    $surepassService = new SurepassService();
    $result = $surepassService->getGstinByPan($request->pan_number);

    if (!$result['success']) {
        return response()->json($result);
    }

    // Parse the GSTIN data
    $gstinList = $surepassService->parseGstinData($result['data']);

    if (empty($gstinList)) {
        return response()->json([
            'success' => false,
            'message' => 'No GSTIN found for this PAN'
        ]);
    }

    // Don't save automatically - let user select which GSTINs to save
    return response()->json([
        'success' => true,
        'data' => $gstinList,
        'message' => 'GSTIN details fetched successfully'
    ]);
}

/**
 * Save selected GSTINs for a client
 */
public function saveSelectedGstins(Request $request)
{
    $request->validate([
        'client_id' => 'required|integer|exists:clients,id',
        'gstins' => 'required|array|min:1',
        'gstins.*.gstin' => 'required|string|size:15',
        'gstins.*.trade_name' => 'nullable|string',
        'gstins.*.legal_name' => 'nullable|string',
        'gstins.*.principal_business_address' => 'nullable|string',
        'gstins.*.building_name' => 'nullable|string',
        'gstins.*.building_number' => 'nullable|string',
        'gstins.*.floor_number' => 'nullable|string',
        'gstins.*.street' => 'nullable|string',
        'gstins.*.location' => 'nullable|string',
        'gstins.*.district' => 'nullable|string',
        'gstins.*.city' => 'nullable|string',
        'gstins.*.state' => 'nullable|string',
        'gstins.*.state_code' => 'nullable|string|max:2',
        'gstins.*.pincode' => 'nullable|string|max:10',
        'gstins.*.is_primary' => 'nullable|boolean',
    ]);

    try {
        // If a GSTIN is marked as primary, unmark all others
        $hasPrimary = collect($request->gstins)->contains('is_primary', true);
        
        if ($hasPrimary) {
            Gstin::where('entity_type', 'client')
                ->where('entity_id', $request->client_id)
                ->update(['is_primary' => false]);
        }

        $conflicts = [];
        $savedCount = 0;

        // Save each selected GSTIN with duplicate checks across entities
        foreach ($request->gstins as $gstinData) {
            $existingOther = Gstin::where('gstin', $gstinData['gstin'])
                ->where(function($q) use ($request) {
                    $q->where('entity_type', '!=', 'client')
                      ->orWhere('entity_id', '!=', $request->client_id);
                })
                ->first();

            if ($existingOther) {
                $conflicts[] = $gstinData['gstin'];
                continue; // skip saving this GSTIN
            }

            Gstin::updateOrCreate(
                [
                    'entity_type' => 'client',
                    'entity_id' => $request->client_id,
                    'gstin' => $gstinData['gstin']
                ],
                [
                    'trade_name' => $gstinData['trade_name'] ?? null,
                    'legal_name' => $gstinData['legal_name'] ?? null,
                    'principal_business_address' => $gstinData['principal_business_address'] ?? null,
                    'building_name' => $gstinData['building_name'] ?? null,
                    'building_number' => $gstinData['building_number'] ?? null,
                    'floor_number' => $gstinData['floor_number'] ?? null,
                    'street' => $gstinData['street'] ?? null,
                    'location' => $gstinData['location'] ?? null,
                    'district' => $gstinData['district'] ?? null,
                    'city' => $gstinData['city'] ?? null,
                    'state' => $gstinData['state'] ?? null,
                    'state_code' => $gstinData['state_code'] ?? null,
                    'pincode' => $gstinData['pincode'] ?? null,
                    'status' => 'Active',
                    'is_primary' => $gstinData['is_primary'] ?? false,
                ]
            );
            $savedCount++;
        }

        $message = $savedCount . ' GSTIN(s) saved successfully';
        if (!empty($conflicts)) {
            $message .= '. Skipped duplicates already linked to other entities: ' . implode(', ', $conflicts);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error saving GSTINs: ' . $e->getMessage()
        ], 500);
    }
}

// client send password

public function sendPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'user_name' => 'required|string|max:255',
        'client_name' => 'nullable|string|max:255',
    ]);

    $email = $request->email;
    $client = Client::where('support_spoc_email', $email)->first();

    if (!$client) {
        $client = new Client();
        $client->support_spoc_email = $email;
        $client->client_name = $request->client_name ?: $request->user_name;
        $lastClientId = Client::latest('id')->value('id') ?? 0;
        $client->client_code = 'CLI' . str_pad($lastClientId + 1, 3, '0', STR_PAD_LEFT);
        $client->status = 'Active';
        $client->portal_active = 1;
    }

    // Generate random password
    $password = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789@#$%'), 0, 10);

    // Update client table with hashed password
    $client->portal_password = Hash::make($password);
    $client->user_name = $request->user_name;
    $client->save();

    // Send password email
    Mail::raw("Dear Client,
    \n\nYour portal login credentials:
    \nUsername: {$request->user_name}
    \nPassword: $password  
    \n\nLogin URL: https://one.unborn.in/client/login
    \n\nThank you!", function ($msg) use ($email) {
        $msg->to($email)->subject('Client Portal Credentials');
    });

    return response()->json(['success' => true]);
}

}
