<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // SLA Monthly calculation
        $schedule->call(function () {
            foreach (\App\Models\ClientLink::all() as $link) {
                app(\App\Http\Controllers\SlaReportController::class)->calculate($link->id);
            }
        })->monthlyOn(1, '01:30');
        $schedule->job(new \App\Jobs\CollectLinkMetrics)->everyFiveMinutes();

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
