<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\Einvoice\EinvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateEinvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function handle(EinvoiceService $service)
    {
        $service->generate($this->invoice);
    }
}
