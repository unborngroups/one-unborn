<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::latest()->get();
        return view('finance.purchases.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'gst_number' => 'nullable|string|max:50',
        'address' => 'nullable|string',
    ]);

    Vendor::create($request->all());

    return redirect()
        ->route('finance.purchases.vendors.index')
        ->with('success', 'Vendor Created Successfully');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $vendor = Vendor::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|max:20',
        'gst_number' => 'nullable|string|max:50',
        'address' => 'nullable|string',
    ]);

    $vendor->update($request->all());

    return redirect()
        ->route('finance.purchases.vendors.index')
        ->with('success', 'Vendor Updated Successfully');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return back()->with('success', 'Vendor Deleted');
    }
}
