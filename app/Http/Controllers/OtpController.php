<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\OtpMail;

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid email.'], 422);
        }
        $email = $request->email;
            $user = \App\Models\User::where('official_email', $email)->orWhere('email', $email)->first();
            $requireOtpAlways = $user ? (bool)$user->require_otp_always : false;
            if ($requireOtpAlways) {
                $otp = rand(100000, 999999);
                // Store OTP in cache for 5 minutes
                Cache::put('otp_' . $email, $otp, now()->addMinutes(5));
                // Send OTP mail from company settings
                $company = \App\Models\CompanySetting::first();
                $fromEmail = $company->email ?? config('mail.from.address');
                $fromName = $company->company_name ?? config('mail.from.name');
                Mail::to($email)->send((new OtpMail($otp))->from($fromEmail, $fromName));
                return response()->json(['status' => true, 'message' => 'OTP sent to your email.', 'require_otp_always' => true]);
            } else {
                return response()->json(['status' => true, 'message' => 'OTP not required for this user.', 'require_otp_always' => false]);
            }
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Invalid input.'], 422);
        }
        $email = $request->email;
        $otp = $request->otp;
        $cachedOtp = Cache::get('otp_' . $email);
        Log::info('[OTP VERIFY]', [
            'email' => $email,
            'otp_entered' => $otp,
            'cached_otp' => $cachedOtp,
        ]);
        if ($cachedOtp && $cachedOtp == $otp) {
            // Optionally, remove OTP after successful verification
            Cache::forget('otp_' . $email);
            // Mark email as verified in session
            session(['otp_verified_email' => $email]);
            return response()->json(['status' => true, 'message' => 'OTP verified.']);
        }
        return response()->json(['status' => false, 'message' => 'Invalid or expired OTP.'], 401);
    }
}
