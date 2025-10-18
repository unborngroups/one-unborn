<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
{
    $profile = Auth::user(); // directly get logged-in user details
    return view('profile.index', compact('profile'));
}
    public function create()
    {
        return view('profile.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to update profile.');
        }

        // ✅ Validate all inputs
        $validated = $request->validate([
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'Date_of_Birth'    => 'required|date',
            'email' => 'required|email',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'aadhaar_number' => 'required|string|max:20',
            'aadhaar_upload' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'pan' => 'required|string|max:20',
            'pan_upload' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'bank_name' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
        ]);
         // ✅ Convert DOB format before saving (if needed)
        if (!empty($request->Date_of_Birth)) {
            try {
                $validated['Date_of_Birth'] = Carbon::createFromFormat('Y-m-d', $request->Date_of_Birth)->format('Y-m-d');
            } catch (\Exception $e) {
                $validated['Date_of_Birth'] = Carbon::createFromFormat('d-m-Y', $request->Date_of_Birth)->format('Y-m-d');
            }
        }
        // ✅ Handle file uploads and save to public/images/...
        if ($request->hasFile('aadhaar_upload')) {
            $aadhaarFile = $request->file('aadhaar_upload');
            $aadhaarName = time() . '_aadhaar_' . $aadhaarFile->getClientOriginalName();
            $aadhaarFile->move(public_path('images/aadhaar'), $aadhaarName);
            $validated['aadhaar_upload'] = 'images/aadhaar/' . $aadhaarName;
        }

        if ($request->hasFile('pan_upload')) {
            $panFile = $request->file('pan_upload');
            $panName = time() . '_pan_' . $panFile->getClientOriginalName();
            $panFile->move(public_path('images/pan'), $panName);
            $validated['pan_upload'] = 'images/pan/' . $panName;
        }

           /** @var \App\Models\User $user */
        
    // ✅ Create profile
    $user->profile()->create($validated);

    // ✅ Update user to mark profile as created
    $user->update(['profile_created' => true]);

        return redirect()->route('welcome')->with('success', 'Profile completed successfully!');
    }
}
