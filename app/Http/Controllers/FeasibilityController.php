<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Feasibility;
use App\Models\FeasibilityStatus;

class FeasibilityController extends Controller
{
    public function index()
    {
        // $feasibilities = Feasibility::all();
        $feasibilities = Feasibility::with('client')->get();

        $permissions = TemplateHelper::getUserMenuPermissions('User Type') ?? (object)[
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
        $clients = Client::all();
        return view('feasibility.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_of_service' => 'required',
            'client_id' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'required',
            'spoc_contact1' => 'required',
            'no_of_links' => 'required',
            'vendor_type' => 'required',
            'speed' => 'required',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Feasibility::create($validated);

        $feasibility = Feasibility::create($validated);

        // ⚙️ Automatically create feasibility status entry for Operations
        FeasibilityStatus::create([
            'feasibility_id' => $feasibility->id,
            'status' => 'Open',
        ]);

        return redirect()->route('feasibility.index')->with('success', 'Feasibility added successfully!');
    }

    public function edit($id)
{
    $feasibility = Feasibility::findOrFail($id);
    $clients = Client::all(); // ✅ Add this line

    return view('feasibility.edit', compact('feasibility', 'clients'));
}


    public function update(Request $request, Feasibility $feasibility)
    {
        $validated = $request->validate([
            'type_of_service' => 'required',
            'client_id' => 'required',
            'pincode' => 'required',
            'state' => 'required',
            'district' => 'required',
            'area' => 'required',
            'address' => 'required',
            'spoc_name' => 'required',
            'spoc_contact1' => 'required',
            'no_of_links' => 'required',
            'vendor_type' => 'required',
            'speed' => 'required',
            'status' => 'required|in:Active,Inactive',
        ]);

        

        $feasibility->update($validated);
        return redirect()->route('feasibility.index')->with('success', 'Feasibility updated successfully!');
    }

    public function destroy(Feasibility $feasibility)
    {
        $feasibility->delete();
        return redirect()->route('feasibility.index')->with('success', 'Feasibility deleted successfully!');
    }

    public function view($id)
    {
        $feasibility = Feasibility::with('client', 'feasibilityStatus')->findOrFail($id);
        return view('feasibility.view', compact('feasibility'));
    }

     public function toggleStatus($id)
{
    $feasibility = Feasibility::findOrFail($id);

    // Toggle Active/Inactive
    $feasibility->status = $feasibility->status === 'Active' ? 'Inactive' : 'Active';
    $feasibility->save();

    return redirect()->route('feasibility.index')
                     ->with('success', 'Feasibility status updated successfully.');
}
}
