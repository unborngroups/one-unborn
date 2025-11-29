<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use App\Models\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         /** ⬇️ IMPORTANT - Fix created_at & updated_at timezone issue */
        DB::statement("SET time_zone = '+05:30'");   // IST Time
         // 🟩 Auto-share menus with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                // $userType = Auth::user()->userType->name ?? 'Employee';

                // $menus = Menu::where('user_type', $userType)
                //              ->where('can_view', true)
                //              ->get();
                // ✅ Get correct user_type (based on column or relation)
                $user = Auth::user();
                $userType = strtolower($user->user_type ?? ($user->userType->name ?? 'users'));

                $user = Auth::user();


                // Fetch menus belonging to this user_type or global 'All' menus
                $menusForType = Menu::whereIn('user_type', [$userType, 'All', 'all'])
                             ->orderBy('id')
                             ->get();

                // Only show menus when an explicit UserMenuPrivilege exists with can_menu = 1.
                // This prevents menus from showing just because menu->can_view is true.
                $menus = $menusForType->filter(function ($menu) use ($user) {
                    $priv = $menu->privileges()->where('user_id', $user->id)->first();
                    return $priv ? (bool) $priv->can_menu : false;
                })->values();

                $view->with('menus', $menus);
            } else {
                $view->with('menus', collect());
            }
        });
    }
}
