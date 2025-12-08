<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VendorMake;
use App\Models\VendorModel;

class VendorMakeController extends Controller
{
     // Return make details for AJAX
    public function show($id)
    {
        $make = VendorMake::findOrFail($id);
        return response()->json([
            'id' => $make->id,
            'make_name' => $make->make_name,
            'company_name' => $make->company_name,
            'contact_no' => $make->contact_no,
            'email_id' => $make->email_id,
        ]);
    }
    
public function create()
{
    $makes = VendorMake::all();
    return view('feasibility.create', compact('makes'));
}


}
