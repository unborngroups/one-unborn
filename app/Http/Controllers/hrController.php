<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\TemplateHelper;

class hrController extends Controller
{
    public function index()
    {
        $permissions = TemplateHelper::getUserMenuPermissions('Employee') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ]; 

        $users = User::with('profile')
            ->orderBy('id', 'asc')
            ->get();

        return view('hr.index', compact('users', 'permissions'));
    }

    public function show($id)
    {
        $user = User::with('profile')->findOrFail($id);

        return view('hr.view', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);

        return view('hr.edit', compact('user'));
    }
}
