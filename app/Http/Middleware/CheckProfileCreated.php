<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCreated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 🚫 ALL users (including superusers and admins) must create profile first
        if (!$user->profile_created) {
            if (
                !$request->is('profile/create') &&
                !$request->is('profile/store') &&
                !$request->is('logout')
            ) {
                return redirect()
                    ->route('profile.create')
                    ->with('alert', 'Please complete your profile before accessing the dashboard.');
            }
        }
        return $next($request);
    }
}
