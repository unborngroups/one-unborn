<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('finance.settings.index');
    }

    public function gst()
    {
        return view('finance.settings.gst');
    }

    public function tds()
    {
        return view('finance.settings.tds');
    }
}
