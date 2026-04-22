<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\UserMenuPrivilege;
use App\Models\Menu;

class PrivilegeHelper
{
    /**
     * Check if the current user has privilege (add, edit, delete, view)
     * for a given route.
     */
    public static function can($route, $action)
    {
        $user = Auth::user();
        if (!$user) return false;

        // 🟢 Get the menu for that route
        $menu = Menu::where('route', $route)->first();
        if (!$menu) return false;

        // 🟢 Find the privilege entry for this user + menu
        $privilege = UserMenuPrivilege::where('user_id', $user->id)
                    ->where('menu_id', $menu->id)
                    ->first();

        if (!$privilege) return false;

        // ✅ Check based on user privilege record, not menu table
        return match ($action) {
            'add'    => (bool) $privilege->can_add,
            'edit'   => (bool) $privilege->can_edit,
            'delete' => (bool) $privilege->can_delete,
            'view'   => (bool) $privilege->can_view,
            default  => false,
        };
    }
}
