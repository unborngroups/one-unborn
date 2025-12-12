<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\LoginLog;
use Carbon\Carbon;
use Carbon\CarbonInterval;

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
         /** ⬇ IMPORTANT - Fix created_at & updated_at timezone issue */
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

        // 
        view()->composer('*', function ($view) {
            if (Auth::check()) {
                $log = LoginLog::where('user_id', Auth::id())
                    ->where('status', 'Online')
                    ->latest()
                    ->first();

                $onlineSince = null;
                $onlineMinutes = null;
                $onlineDurationLabel = null;
                $localClock = Carbon::now(config('app.timezone'));
                $cloudClock = Carbon::now('Asia/Kolkata');
                $clockDisplay = sprintf(
                    '%s | Cloud Server Time (GMT+5:30) : %s',
                    $localClock->format('l, F j, Y h:i:s A'),
                    $cloudClock->format('D, d-M-Y h:i:s A')
                );

                if ($log) {
                    $onlineSince = Carbon::parse($log->login_time)->format('h:i A');

                    $secondsOnline = Carbon::parse($log->login_time)->diffInSeconds(now());
                    $secondsOnline = max($secondsOnline, 0);
                    $onlineMinutes = round($secondsOnline / 60, 1);

                    $interval = CarbonInterval::seconds($secondsOnline)->cascade();
                    $parts = [];
                    if ($interval->h) {
                        $parts[] = $interval->h . 'h';
                    }
                    if ($interval->i) {
                        $parts[] = $interval->i . 'm';
                    }
                    if ($interval->s || empty($parts)) {
                        $parts[] = $interval->s . 's';
                    }
                    $onlineDurationLabel = implode(' ', $parts);
                }

                $view->with([
                    'onlineSince' => $onlineSince,
                    'onlineMinutes' => $onlineMinutes,
                    'onlineDurationLabel' => $onlineDurationLabel,
                    'onlineLoginTimeIso' => $log ? Carbon::parse($log->login_time)->toIso8601String() : null,
                    'clockDisplay' => $clockDisplay,
                ]);
            }
        });
    }

}