<?php

namespace App\Mail;

use App\Console\encription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Emailcharges extends Mailable
{
    use Queueable, SerializesModels;
    protected $charp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($charp)
    {
        $this->charp = $charp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $charp= $this->charp;
        return $this->markdown('email.charges',['charp' => $charp])->subject(   $charp['username'].' |Account Being Charge |'.'Reno-'. $charp['refid']);
    }
}
