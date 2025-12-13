<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;

class ForceLogoutIfOffline
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next): Response
    {

        if (Auth::check()) {

            $log = LoginLog::where('user_id', Auth::id())
                ->where('status', 'Online')
                ->whereNull('logout_time')
                ->latest()
                ->first();

                 // ❌ No active session found → force logout
            if (!$log) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('login')
                    ->withErrors(['session' => 'Session expired. Please login again.']);
            }
        }

        return $next($request);
    }
}
