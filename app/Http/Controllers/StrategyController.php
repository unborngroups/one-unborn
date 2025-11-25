<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function index()
    {
        return view('strategy.index');
    }
}
