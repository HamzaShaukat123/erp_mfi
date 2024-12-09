<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject('OTP for New Device Login') // Set the subject of the email
        ->view('emails.otp') // Set the view for the email content
        ->with([
            'details' => $this->details, // Pass the OTP to the view
        ]);
    }
}
