<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\LoginLog;

class AuthController extends Controller
{
    // public function showLogin()
    // {
    //     return view('auth.login');
    // }
    
public function showLogin()
{
    $company = \App\Models\CompanySetting::first(); // company settings fetch

    return view('auth.login', compact('company'));
}

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $activeCredentials = array_merge($credentials, ['status' => 'Active']);

        // if (Auth::attempt($credentials)) {
        //     return redirect()->route('welcome'); // after login, redirect to users page
        // }

        if (Auth::attempt($activeCredentials)) {
            $user = Auth::user();

            LoginLog::create([
                'user_id' => $user->id,
                'login_time' => now(),
                'last_activity' => now(),
                'status' => 'Online',
            ]);

            // 👇 Check if profile is created or not
            if (!$user->profile_created) {
                return redirect()->route('profile.create')
                                 ->with('info', 'Please complete your profile before continuing.');
            }

            // ✅ If profile already created, go to dashboard
            return redirect()->route('welcome');
        }

        return back()->withErrors(['email' => 'Invalid credentials or the account is inactive.']);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $log = LoginLog::where('user_id', Auth::id())
                ->where('status', 'Online')
                ->latest()
                ->first();

            if ($log) {
                $logoutTime = now();
                $minutes = $log->login_time->diffInMinutes($logoutTime);

                $log->update([
                    'logout_time' => $logoutTime,
                    'total_minutes' => $minutes,
                    'last_activity' => now(),
                    'status' => 'Offline',
                ]);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    
//password

public function changePasswordForm()
{
    return view('users.change-password');
}

public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = Auth::user();

    // Check current password
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    // Update password
    /** @var \App\Models\User $user */
    $user = Auth::user();   

    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->route('users.index')->with('success', 'Password updated successfully!');
}


}