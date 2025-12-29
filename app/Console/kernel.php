<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // ✅ Monthly SLA calculation (ONLY ONE)
        $schedule->command('sla:calculate-monthly')
                 ->monthlyOn(1, '00:15');

        // ✅ Collect live link metrics
        $schedule->job(new \App\Jobs\CollectLinkMetrics)
                 ->everyFiveMinutes();

        // ✅ Mark stale logins offline
        $schedule->call(function () {
            \App\Models\LoginLog::markStaleOffline();
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
    