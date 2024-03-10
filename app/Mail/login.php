<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class login extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $login;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($login)
    {
        $this->login = $login;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $login= $this->login;
        return $this->markdown('email.login',['login' => $login])->subject(   $login.' Successfully Login');
    }
}
