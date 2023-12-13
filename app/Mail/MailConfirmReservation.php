<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailConfirmReservation extends Mailable
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
            $ref_no, 
            $user_name, 
            $user_email, 
            $user_contact, 
            $resort_address, 
            $note;

    public function __construct($resort_name, 
                                $price_desc,  
                                $time_from, 
                                $time_to,
                                $reserve_date, 
                                $ref_no, 
                                $user_name, 
                                $user_email, 
                                $user_contact, 
                                $resort_address, 
                                $note)
    {
        $this->resort_name = $resort_name;
        $this->price_desc = $price_desc;
        $this->time_from = $time_from;
        $this->time_to = $time_to;
        $this->reserve_date = $reserve_date;
        $this->ref_no = $ref_no;
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->user_contact = $user_contact;
        $this->resort_address = $resort_address;
        $this->note = $note;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->subject('Reservation confirmed')
                ->view('emails.reserve_confirmed');
    }
}
