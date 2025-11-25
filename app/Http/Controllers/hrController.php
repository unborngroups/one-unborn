<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class hrController extends Controller
{
    public function index()
    {
        return view('hr.index');
    }
}
