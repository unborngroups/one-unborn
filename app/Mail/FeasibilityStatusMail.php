<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeasibilityStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feasibility;
    public $status;
    public $previousStatus;
    public $actionBy;
    public $emailType; // created OR status_update

    /**
     * Create a new message instance.
     */
    public function __construct($feasibility, $status, $previousStatus = null, $actionBy = null, $emailType = 'status_update')
    {
        $this->feasibility     = $feasibility;
        $this->status          = $status;
        $this->previousStatus  = $previousStatus;
        $this->actionBy        = $actionBy;
        $this->emailType       = $emailType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $templateContent = null;
        if ($this->status === 'Closed') {
            $template = \App\Models\EmailTemplate::where('event_key', 'feasibility_completed')->where('status', 1)->first();
            if ($template) {
                // Prepare data for placeholder replacement
                $data = [
                    'feasibility_id' => $this->feasibility->feasibility_request_id ?? '',
                    'client_name' => $this->feasibility->client->client_name ?? '',
                    'company_name' => $this->feasibility->company->company_name ?? '',
                    'type_of_service' => $this->feasibility->type_of_service ?? '',
                    'address' => $this->feasibility->address ?? '',
                    'speed' => $this->feasibility->speed ?? '',
                    'static_ip' => $this->feasibility->static_ip ?? '',
                    'pincode' => $this->feasibility->pincode ?? '',
                    'spoc_name' => $this->feasibility->spoc_name ?? '',
                    'spoc_email' => $this->feasibility->spoc_email ?? '',
                    'status' => $this->status,
                    'action_by' => $this->actionBy->name ?? '',
                    'previous_status' => $this->previousStatus ?? '',
                    'creator_name' => $this->feasibility->creator->name ?? '',
                ];
                $templateContent = \App\Helpers\TemplateHelper::renderTemplate($template->body, $data);
            }
        }
        return $this->subject($this->getEmailSubject())
            ->view('emails.feasibility.status')
            ->with([
                'feasibility' => $this->feasibility,
                'status' => $this->status,
                'previousStatus' => $this->previousStatus,
                'actionBy' => $this->actionBy,
                'templateContent' => $templateContent,
            ]);
    }

    /**
     * Determine subject line dynamically.
     */
    private function getEmailSubject()
    {
        // Case 1: Feasibility Newly Created by Sales → Goes to Team
        if ($this->emailType === 'created') {
            return 'New Feasibility Created - Action Required';
        }

        // Case 2: Operations Updated Status → Goes to Creator
        switch ($this->status) {
            case 'Open':
                return 'Feasibility Updated - Now Open';

            case 'InProgress':
            case 'In Progress':
                return 'Feasibility Updated - Now In Progress';

            case 'Closed':
                return 'Feasibility Completed - ' . ($this->feasibility->feasibility_request_id ?? '');

            default:
                return 'Feasibility Status Updated';
        }
    }
}
