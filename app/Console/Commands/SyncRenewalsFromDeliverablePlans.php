<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeliverablePlan;
use App\Models\Renewal;
use Carbon\Carbon;

class SyncRenewalsFromDeliverablePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-renewals-from-deliverable-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $plans = DeliverablePlan::all();

    foreach ($plans as $plan) {

        // Check already exists
        $exists = Renewal::where('deliverable_id', $plan->deliverable_id)
            ->where('circuit_id', $plan->circuit_id)
            ->exists();

        if ($exists) continue;

        $renewalDate = Carbon::now();

        $expiry = $renewalDate->copy()
            ->addDays(30) // default 1 month
            ->subDay();

        $alertDate = $expiry->copy()->subDay();

        Renewal::create([
            'deliverable_id'   => $plan->deliverable_id,
            'circuit_id'       => $plan->circuit_id,
            'date_of_renewal'  => $renewalDate,
            'renewal_months'   => 1,
            'new_expiry_date'  => $expiry,
            'alert_date'       => $alertDate,
        ]);
    }

    $this->info('Renewals synced successfully ✅');
    }
}
