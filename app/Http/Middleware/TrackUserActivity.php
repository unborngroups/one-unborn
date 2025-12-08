<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;

class TrackUserActivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {

            $log = LoginLog::where('user_id', Auth::id())
                ->where('status', 'Online')
                ->latest()
                ->first();

            if ($log) {
                $minutes = $log->login_time->diffInMinutes(now());
                $log->update([
                    'total_minutes' => $minutes
                ]);
            }
        }

        return $next($request);
    }
}
