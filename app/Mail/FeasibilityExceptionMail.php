<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeasibilityExceptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feasibilityStatus;
    public $vendorName;
    public $vendorDetails;
    public $sentBy;

    public function __construct($feasibilityStatus, string $vendorName, array $vendorDetails = [], $sentBy = null)
    {
        $this->feasibilityStatus = $feasibilityStatus;
        $this->vendorName = $vendorName;
        $this->vendorDetails = $vendorDetails;
        $this->sentBy = $sentBy;
    }

    public function build()
    {
        $feasibility = $this->feasibilityStatus->feasibility;

        return $this->subject('Feasibility Exception - ' . ($feasibility->feasibility_request_id ?? ''))
            ->view('emails.feasibility.exception')
            ->with([
                'feasibilityStatus' => $this->feasibilityStatus,
                'feasibility' => $feasibility,
                'vendorName' => $this->vendorName,
                'vendorDetails' => $this->vendorDetails,
                'sentBy' => $this->sentBy,
            ]);
    }
}
