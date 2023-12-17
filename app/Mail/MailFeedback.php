<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailFeedback extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $feedback, 
            $name,
            $subject,
            $email;

    public function __construct($feedback, $name, $subject, $email)
    {
        $this->feedback = $feedback;
        $this->name = $name;
        $this->subject = $subject;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                    ->subject($this->subject)
                    ->view('emails.feedback');
    }
}
