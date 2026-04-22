<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LoginLog;

class UserActivityController extends Controller
{
    public function heartbeat(Request $request)
    {
        if (Auth::check()) {
            $log = LoginLog::where('user_id', Auth::id())
                ->where('status', 'Online')
                ->latest()
                ->first();
            if ($log) {
                $log->update(['last_activity' => now()]);
            }
        }
        return response()->json(['success' => true]);
    }

        public function tabClose(Request $request)
    {
        if (Auth::check()) {
            $log = \App\Models\LoginLog::where('user_id', Auth::id())
                ->where('status', 'Online')
                ->latest()
                ->first();
            if ($log) {
                $log->update([
                    'last_activity' => now(),
                    'logout_time' => now(),
                    'status' => 'Offline',
                ]);
            }
        }
        return response()->json(['success' => true]);
    }
}
