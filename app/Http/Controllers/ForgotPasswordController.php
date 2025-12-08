<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email'
        ]);

        // Find user by official email
        $user = User::where('official_email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found.');
        }

        // Generate token
        $token = Str::random(60);

        // Store token in table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->official_email],
            ['token' => $token, 'created_at' => now()]
        );

        // Get company from email
        $companySettings = CompanySetting::first();
        $fromEmail = $companySettings->company_email ?? config('mail.from.address');
        // Reset URL
        $resetUrl = url('/reset-password/' . $token);

        // Email data
        $data = [
            'user' => $user,
            'resetUrl' => $resetUrl,
        ];

        // Dynamic mail configuration
        config([
            'mail.from.address' => $fromEmail,
            'mail.from.name' => $companySettings->company_name ?? 'Company'
        ]);

        // Send email
        Mail::send('emails.password_reset', $data, function ($message) use ($user) {
            $message->to($user->official_email);
            $message->subject('Password Reset Request');
        });

        return back()->with('success', 'Reset link has been sent to your email.');
    }

    public function showResetForm($token)
{
    $reset = DB::table('password_reset_tokens')->where('token', $token)->first();

    if (!$reset) {
        return redirect('/forgot-password')->with('error', 'Invalid or expired token.');
    }

    return view('auth.reset-password', [
        'token' => $token,
        'email' => $reset->email
    ]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed'
    ]);

    $reset = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if (!$reset) {
        return back()->with('error', 'Invalid or expired token.');
    }

    User::where('official_email', $request->email)->update([
        'password' => bcrypt($request->password)
    ]);

    // Delete token
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return redirect('/login')->with('success', 'Password has been reset. Please login.');
}

}
