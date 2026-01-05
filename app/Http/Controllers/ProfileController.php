<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'Date_of_Birth'    => 'required|date',
            'official_email' => 'required|email',
            'personal_email' => 'required|email',
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
            // 'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        ]);
         // ✅ Convert DOB format before saving (if needed)
        if (!empty($request->Date_of_Birth)) {
            try {
                $validated['Date_of_Birth'] = Carbon::createFromFormat('Y-m-d', $request->Date_of_Birth)->format('Y-m-d');
            } catch (\Exception $e) {
                $validated['Date_of_Birth'] = Carbon::createFromFormat('Y-m-d', $request->Date_of_Birth)->format('Y-m-d');
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

         // ✅ Handle Profile Photo
        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $photoName = time() . '_profile_' . $photoFile->getClientOriginalName();
            $photoFile->move(public_path('images/profile_photos'), $photoName);
            $validated['profile_photo'] = 'images/profile_photos/' . $photoName;
        }

           /** @var \App\Models\User $user */
        if ($user->profile) {
            // ✅ Update existing profile
            $user->profile->update($validated);
        } else {
            // ✅ Create new profile
            $user->profile()->create($validated);
        }
    // ✅ Update user to mark profile as created
    $user->update(['profile_created' => true]);

        return redirect()->route('welcome')->with('success', 'Profile completed successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function view()
{
    $user = Auth::user(); // get logged-in user

    // if user has profile relation, load it
    $profile = $user->profile ?? null;

    // return a simple view (create a new blade file for this if not yet)
    return view('profile.view', compact('user', 'profile'));
}
public function edit()
{
    $user = Auth::user();
    $profile = $user->profile;

    if (!$profile) {
        return redirect()->route('profile.create')
                         ->with('warning', 'Please create your profile first.');
    }

    return view('profile.edit', compact('user', 'profile'));
}



public function update(Request $request)
{
    $user = Auth::user();
    $profile = $user->profile;

    if (!$profile) {
        return redirect()->route('profile.create')
                         ->with('error', 'Profile not found!');
    }

    $validated = $request->validate([
        'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'fname' => 'required|string|max:100',
        'lname' => 'required|string|max:100',
        'designation' => 'required|string|max:100',
        'Date_of_Birth' => 'required', // Remove 'date' rule to allow custom formats
        'official_email' => 'required|email',
        'personal_email' => 'required|email',
        'phone1' => 'required|string|max:20',
        'phone2' => 'nullable|string|max:20',
        'aadhaar_number' => 'required|string|max:20',
        'aadhaar_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'pan' => 'required|string|max:20',
        'pan_upload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    // Convert Date_of_Birth from DD-MM-YYYY to Y-m-d if needed
    if (!empty($validated['Date_of_Birth'])) {
        try {
            // Try DD-MM-YYYY first
            $validated['Date_of_Birth'] = \Carbon\Carbon::createFromFormat('d-m-Y', $validated['Date_of_Birth'])->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                // Try Y-m-d (already correct)
                $validated['Date_of_Birth'] = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['Date_of_Birth'])->format('Y-m-d');
            } catch (\Exception $e2) {
                // Leave as is if both fail
            }
        }
    }

    // Handle file uploads
    if ($request->hasFile('aadhaar_upload')) {
        $aadhaar = $request->file('aadhaar_upload');
        $aadhaarName = time() . '_aadhaar_' . $aadhaar->getClientOriginalName();
        $aadhaar->move(public_path('images/aadhaar'), $aadhaarName);
        $validated['aadhaar_upload'] = 'images/aadhaar/' . $aadhaarName;
    }

    if ($request->hasFile('pan_upload')) {
        $pan = $request->file('pan_upload');
        $panName = time() . '_pan_' . $pan->getClientOriginalName();
        $pan->move(public_path('images/pan'), $panName);
        $validated['pan_upload'] = 'images/pan/' . $panName;
    }

    // Handle profile photo upload
    if ($request->hasFile('profile_photo')) {
        try {
            $photo = $request->file('profile_photo');
            
            // Create directory if it doesn't exist
            $directory = public_path('images/profile_photos');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $photoName = time() . '_profile_' . $photo->getClientOriginalName();
            $photo->move($directory, $photoName);
            $validated['profile_photo'] = 'images/profile_photos/' . $photoName;
            
            Log::info('Profile photo uploaded successfully: ' . $photoName);
        } catch (\Exception $e) {
            Log::error('Profile photo upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error uploading profile photo: ' . $e->getMessage());
        }
    }

    // ✅ Convert Date_of_Birth before updating
if (!empty($validated['Date_of_Birth'])) {
    try {
        $validated['Date_of_Birth'] = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['Date_of_Birth'])->format('Y-m-d');
    } catch (\Exception $e) {
        // If already in correct format, ignore
    }
}

    $profile->update($validated);

    return redirect()->route('profile.view')->with('success', 'Profile updated successfully!');
}


}
