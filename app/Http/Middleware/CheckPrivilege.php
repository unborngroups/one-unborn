<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use App\Models\UserMenuPrivilege;

class CheckPrivilege
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $action)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        // Get current route name
        $routeName = $request->route()->getName();

        // Find menu by route name
        $menu = Menu::where('route', $routeName)->first();
        if (!$menu) {
            return $next($request); // If no menu defined, allow access
        }

        // Fetch privilege for this menu & user
        $privilege = UserMenuPrivilege::where('user_id', $user->id)
            ->where('menu_id', $menu->id)
            ->first();

        // 🟡 Check menu access first
        if (!$privilege || !$privilege->can_menu) {
            return redirect()->route('welcome')->with('error', 'Access Denied: Menu not available.');
        }

        // 🔵 Then check specific permission type
        switch ($action) {
            case 'view':
                if (!$privilege->can_view) {
                    return redirect()->route('welcome')->with('error', 'You do not have view access.');
                }
                break;

            case 'edit':
                if (!$privilege->can_edit) {
                    return redirect()->route('welcome')->with('error', 'You do not have edit access.');
                }
                break;

            case 'delete':
                if (!$privilege->can_delete) {
                    return redirect()->route('welcome')->with('error', 'You do not have delete access.');
                }
                break;
        }

        return $next($request);
    }
}
