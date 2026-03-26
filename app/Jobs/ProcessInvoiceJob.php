<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Dispatchable;
use App\Models\EmailLog;
use App\Models\PurchaseInvoice;
use App\Services\InvoiceParserService;
use App\Services\VendorResolverService;
use Illuminate\Support\Facades\DB;

class ProcessInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailId;

    /**
     * Create a new job instance.
     */
    public function __construct($emailId)
    {
        $this->emailId = $emailId;
    }

    /**
     * Execute the job.
     */
    // public function handle()
    // {
    //     $email = EmailLog::find($this->emailId);

    //     if (!$email) return;

    //     // For now just mark processed
    //     $email->update([
    //         'status' => 'processed'
    //     ]);
    // }

    public function failed(\Throwable $exception)
    {
        EmailLog::where('id', $this->emailId)
            ->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage()
            ]);
    }
    

    // 

    public function handle()
{
    DB::transaction(function () {

        $email = EmailLog::findOrFail($this->emailId);

        $parser = app(InvoiceParserService::class);
        $data = $parser->parse(storage_path('app/'.$email->attachment_path));

        $resolver = app(VendorResolverService::class);
        $vendor = $resolver->resolve($data);

        // Duplicate check
        $exists = PurchaseInvoice::where('invoice_number', $data['invoice_number'])
            ->where(function($q) use ($data) {
                $q->where('gstin', $data['gstin'])
                  ->orWhere('vendor_name_raw', $data['vendor_name']);
            })->exists();

        if ($exists) {
            PurchaseInvoice::create([
                'vendor_name_raw' => $data['vendor_name'],
                'invoice_number' => $data['invoice_number'],
                'status' => 'duplicate'
            ]);
            return;
        }

        $status = ($vendor && $data['confidence'] > config('invoice.confidence_threshold'))
            ? 'draft'
            : 'needs_review';

        PurchaseInvoice::create([
            'vendor_name_raw' => $data['vendor_name'],
            'gstin' => $data['gstin'],
            'vendor_id' => $vendor?->id,
            'invoice_number' => $data['invoice_number'],
            'amount' => $data['amount'],
            'tax_amount' => $data['tax'],
            'total_amount' => $data['total'],
            'raw_json' => json_encode($data),
            'status' => $status,
            'confidence_score' => $data['confidence']
        ]);

        $email->update(['status' => 'processed']);
    });
}
}
