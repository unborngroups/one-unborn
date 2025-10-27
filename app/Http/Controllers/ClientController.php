<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Helpers\TemplateHelper;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
       $user = Auth::user();

    // ✅ Superadmin (1) & Admin (2) can see all clients
    if (in_array($user->user_type_id, [1, 2])) {
        $clients = \App\Models\Client::latest()->paginate(10);
    } 
    else {
        // ✅ Normal users: only clients belonging to their assigned companies
        $companyIds = $user->companies()->pluck('companies.id');

        $clients = \App\Models\Client::whereIn('company_id', $companyIds)
                    ->latest()
                    ->paginate(10);
    }
        // Use the helper correctly
        $permissions = TemplateHelper::getUserMenuPermissions('Client Master') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
];
        return view('clients.index', compact('clients', 'permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validated =  $request->validate([
            'client_name'          => 'required|string|max:255',
            'client_code'          => 'nullable|string|max:50|unique:clients,client_code',
            'business_display_name'=> 'nullable|string|max:255',
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
            'invoice_cc'           => 'nullable|email|max:255',

            // Technical Support
            'support_spoc_name'    => 'nullable|string|max:255',
            'support_spoc_mobile'  => 'nullable|string|max:20',
            'support_spoc_email'   => 'nullable|email|max:255',

            'status'           => 'required|in:Active,Inactive',
        ]);

        // ✅ Auto-generate client_code if not provided
    if (empty($validated['client_code'])) {
        $lastClient = Client::latest('id')->first();
        $validated['client_code'] = 'CL' . str_pad(($lastClient->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }
Client::create($validated);

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
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
        'client_name'          => 'required|string|max:255',
        'business_display_name'=> 'nullable|string|max:255',
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
        'invoice_cc'           => 'nullable|email|max:255',

        // Technical Support
        'support_spoc_name'    => 'nullable|string|max:255',
        'support_spoc_mobile'  => 'nullable|string|max:20',
        'support_spoc_email'   => 'nullable|email|max:255',

        'status'               => 'required|in:Active,Inactive',
        ]);
             
        // Don’t allow updating client_code
    $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
       $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
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

}
