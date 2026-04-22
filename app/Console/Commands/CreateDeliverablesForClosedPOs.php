<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseOrder;
use App\Models\Deliverables;
use App\Http\Controllers\DeliverablesController;

class CreateDeliverablesForClosedPOs extends Command
{
    protected $signature = 'deliverables:create-from-closed-pos';
    protected $description = 'Create deliverables for all closed POs that don\'t have deliverables yet';

    public function handle()
    {
        $this->info('Checking for closed POs without deliverables...');

        $closedPOs = PurchaseOrder::where('status', 'Closed')
            ->whereNotIn('po_number', function($query) {
                $query->select('po_number')
                      ->from('deliverables')
                      ->whereNotNull('po_number');
            })
            ->get();

        if ($closedPOs->isEmpty()) {
            $this->info('No closed POs found that need deliverables.');
            return 0;
        }

        $this->info("Found {$closedPOs->count()} closed PO(s) without deliverables.");

        $controller = app(DeliverablesController::class);
        $created = 0;

        foreach ($closedPOs as $po) {
            $this->line("Processing PO #{$po->po_number} (ID: {$po->id})...");
            
            try {
                $controller->createFromPurchaseOrder($po);
                $created++;
                $this->info("  ✓ Deliverable created");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed: " . $e->getMessage());
            }
        }

        $this->info("\nCompleted! Created {$created} deliverable(s).");
        return 0;
    }
}
