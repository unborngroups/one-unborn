<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Models\User;

class CustomUserMail extends Mailable
{
    use Queueable, SerializesModels;


    public $emailData;

    /**
     * Create a new message instance.
     *
     * @param User   $user
     * @param string $template
     * @param array  $emailData
     */

public function __construct($emailData)
{
    $this->emailData = $emailData;
}

    /**
     * Build the message.
     */

// public function build()
// {
//     return $this->view('emails.dynamic-template')
//                 ->with([
//                     'emailData' => $this->emailData,
//                     'logo' => $this->emailData['logo'] ?? null, // ✅ add this line
//                 ])
//                 ->subject($this->emailData['subject']);
// }
 public function build()
    {
        // use config('mail.from') so MailHelper overrides take effect
        $mail = $this->from(config('mail.from.address'), config('mail.from.name'))
                     ->subject($this->emailData['subject'])
                     ->view('emails.dynamic-template')
                     ->with('emailData', $this->emailData);

        return $mail;
    }

}
