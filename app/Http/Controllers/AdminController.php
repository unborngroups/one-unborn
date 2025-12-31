<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginLog;
use Carbon\Carbon;

class AdminController extends Controller
{
   public function index()
{
    $logs = LoginLog::with('user')->orderBy('login_time', 'desc')->get();

    foreach ($logs as $log) {

        $loginTime = Carbon::parse($log->login_time);
        $logoutTime = ($log->status === 'Offline' && $log->logout_time)
        ? Carbon::parse($log->logout_time)
        : now();

        $totalMinutes = $loginTime->diffInMinutes($logoutTime);

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $log->total_minutes = sprintf('%02d:%02d', $hours, $minutes);
    }

    return view('admin.index', compact('logs'));
}

}
