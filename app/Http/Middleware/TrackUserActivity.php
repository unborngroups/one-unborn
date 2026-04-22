<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;

class TrackUserActivity
{
    public function handle($request, Closure $next)
    {
        // 1️⃣ Force inactive users Offline
        LoginLog::markStaleOffline();

        // 2️⃣ Update current user's activity
        if (Auth::check()) {

            $log = LoginLog::where('user_id', Auth::id())
                ->where('status', 'Online')
                ->latest()
                ->first();

            if ($log) {
                $log->update([
                    'last_activity' => now(),
                    'total_minutes' => $log->login_time->diffInMinutes(now())
                ]);
            }
        }

        return $next($request);
    }
}
