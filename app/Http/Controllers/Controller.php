<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\UserMenuPrivilege;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Controller
{
    public static function getUserMenus()
{
    $user = Auth::user();

    if (!$user) return collect();

    // 🟢 Superuser → show all menus
    if ($user->is_superuser) {
        return Menu::where('can_menu', 1)->get();
    }

    // 🟡 Normal users → show menus based on privileges
    return Menu::join('user_menu_privileges', 'menus.id', '=', 'user_menu_privileges.menu_id')
        ->where('user_menu_privileges.user_id', $user->id)
        ->where('user_menu_privileges.can_menu', 1)
        ->select('menus.*')
        ->get();
}
}
