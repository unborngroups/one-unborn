<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Asset_typeController extends Controller
{
   public function index()
    {
        return view('assetmaster.asset_type.index');
    }
}
