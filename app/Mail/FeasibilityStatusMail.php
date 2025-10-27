<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeasibilityStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feasibility;
    public $status;
    /**
     * Create a new message instance.
     */
    public function __construct($feasibility, $status)
    {
        $this->feasibility = $feasibility;
        $this->status = $status;
    }

    /**
     * Get the message building.
     */

    public function build()
    {
        return $this->subject('Feasibility Status Updated')
                    ->markdown('emails.feasibility.status');
    }
    
}
