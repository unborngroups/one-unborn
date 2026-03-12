<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
    public function handle()
    {
        $email = EmailLog::find($this->emailId);

        if (!$email) return;

        // For now just mark processed
        $email->update([
            'status' => 'processed'
        ]);
    }

    public function failed(\Throwable $exception)
    {
        EmailLog::where('id', $this->emailId)
            ->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage()
            ]);
    }
    
}
