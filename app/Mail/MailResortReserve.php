<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailResortReserve extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $resort_name, 
            $price_desc, 
            $time_from, 
            $time_to, 
            $reserve_date, 
            $user_name, 
            $user_email, 
            $user_contact, 
            $screenshot;


    public function __construct($resort_name, 
                                $price_desc, 
                                $time_from, 
                                $time_to, 
                                $reserve_date, 
                                $user_name, 
                                $user_email, 
                                $user_contact, 
                                $screenshot)
    {
        $this->resort_name = $resort_name;
        $this->price_desc = $price_desc;
        $this->time_from = $time_from;
        $this->time_to = $time_to;
        $this->price_desc = $price_desc;
        $this->reserve_date = $reserve_date;
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->user_contact = $user_contact;
        $this->screenshot = $screenshot;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->subject('Resort Reservation')
                ->attach($this->screenshot)
                ->view('emails.resort_reserve');
    }
}
