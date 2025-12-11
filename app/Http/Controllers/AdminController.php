<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginLog;


class AdminController extends Controller
{
    // public function index()
    // {
    //     return view('admin.index');
    // }
public function index()
{
    $logs = LoginLog::with('user')->orderBy('login_time', 'asc')->get();

    foreach ($logs as $log) {
        if ($log->logout_time) {
            // User already logged out
            $log->total_minutes = round((strtotime($log->logout_time) - strtotime($log->login_time)) / 60);
        } else {
            // User is currently online
            $log->total_minutes = round((time() - strtotime($log->login_time)) / 60);
        }
    }

    return view('admin.index', compact('logs'));
}


}
