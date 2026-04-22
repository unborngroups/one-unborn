<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientLink;
use App\Services\SLAService;
use Carbon\Carbon;
class CalculateMonthlySLA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:calculate-monthly {month?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate monthly SLA for all active links';
    /**
     * Execute the console command.
     */
     public function handle()
    {
        $month = $this->argument('month') ?? now()->subMonth()->format('Y-m');

        $this->info("Calculating SLA for {$month}");

        $slaService = new SLAService();

        ClientLink::where('status', 'active')->chunk(100, function ($links) use ($slaService, $month) {
            foreach ($links as $link) {
                try {
                    $sla = $slaService->calculateMonthlySLA($link->id, $month);

                    $this->info("✔ SLA calculated for Link #{$link->id} ({$sla['availability']}%)");
                } catch (\Exception $e) {
                    $this->error("✖ Failed for Link #{$link->id}: " . $e->getMessage());
                }
            }
        });

        $this->info('Monthly SLA calculation completed.');
    }
}
