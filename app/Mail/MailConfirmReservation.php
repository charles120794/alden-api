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

    public $resort_name, $price_desc, $reserve_date, $ref_no, $user_name, $user_email, $user_contact, $resort_address;

    public function __construct($resort_name, $price_desc, $reserve_date, $ref_no, $user_name, $user_email, $user_contact, $resort_address)
    {
        $this->resort_name = $resort_name;
        $this->price_desc = $price_desc;
        $this->reserve_date = $reserve_date;
        $this->ref_no = $ref_no;
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->user_contact = $user_contact;
        $this->resort_address = $resort_address;
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
