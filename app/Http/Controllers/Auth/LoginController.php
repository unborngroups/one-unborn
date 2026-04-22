<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;

class LoginController extends Controller
{
protected function authenticated($request, $user)
{
    LoginLog::create([
        'user_id' => $user->id,
        'login_time' => now(),
        'status' => 'Online'
    ]);
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
                'status' => 'Offline'
            ]);
        }
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}
}