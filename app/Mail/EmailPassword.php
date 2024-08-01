<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $person;

    public $link_client;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password, $person)
    {
        $this->password = $password;
        $this->person = $person;
        $this->link_client = env('APP_ADMIN_CLIENT_URL');
        $this->subject('Cutral Co Digital - Municipalidad de Cutral Co');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.password');
    }
}
