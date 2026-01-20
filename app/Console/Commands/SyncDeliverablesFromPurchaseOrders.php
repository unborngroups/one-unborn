<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseOrder;
use App\Models\Deliverables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SyncDeliverablesFromPurchaseOrders extends Command
{
    protected $signature = 'sync:deliverables-from-pos';
    protected $description = 'Create Deliverables for all Purchase Orders that do not have one.';

    public function handle()
    {
        Log::info('SyncDeliverablesFromPurchaseOrders command started');

        $purchaseOrders = PurchaseOrder::whereNotIn('id', Deliverables::pluck('purchase_order_id')->toArray())->get();
        $controller = App::make(\App\Http\Controllers\PurchaseOrderController::class);
        $count = 0;
        foreach ($purchaseOrders as $po) {
            $controller->createDeliverableFromPurchaseOrder($po);
            $count++;
        }
        $this->info("Created deliverables for $count purchase orders.");
    }
}
