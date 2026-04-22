<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\TemplateHelper;

class AssuranceController extends Controller
{
    public function index()
    {
        // $permissions = TemplateHelper::getUserMenuPermissions('Assurance');
         $permissions = TemplateHelper::getUserMenuPermissions('Assurance') ?? (object)[
    'can_add' => true,
    'can_edit' => true,
    'can_delete' => true,
    'can_view' => true,
         ];
        
        return view('assurance.index', compact('permissions'));
    }
    

    public function create()
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Assurance');
        abort_if(!$permissions->can_add, 403);
        return view('assurance.create');
    }

    public function store(Request $request)
    {
        // TODO: Add form logic when Assurance flow is confirmed
        return back()->with('success', 'Data submitted successfully');
    }
}