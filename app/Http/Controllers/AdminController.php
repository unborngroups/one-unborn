<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginLog;
use Carbon\Carbon;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $logs = LoginLog::with('user')->orderBy('login_time', 'desc')->paginate($perPage);

        foreach ($logs as $log) {
            $loginTime = Carbon::parse($log->login_time);
            if ($log->logout_time) {
                $logoutTime = Carbon::parse($log->logout_time);
                $log->status_display = 'Offline';
                $log->logout_display = $log->logout_time;
            } else {
                $logoutTime = now();
                $log->status_display = 'Online';
                $log->logout_display = 'Active Now';
            }
            $totalMinutes = $loginTime->diffInMinutes($logoutTime);
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $log->total_minutes = sprintf('%02d:%02d', $hours, $minutes);
        }

        return view('admin.index', compact('logs'));
    }

}
