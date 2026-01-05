<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class hrController extends Controller
{
    public function index()
    {
        $users = User::with('profile')
            ->orderBy('id', 'asc')
            ->get();

        return view('hr.index', compact('users'));
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
