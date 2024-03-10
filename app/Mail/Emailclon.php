<?php

namespace App\Mail;

use App\Console\encription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Emailclon extends Mailable
{
    use Queueable, SerializesModels;
    protected $in;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($in)
    {
        $this->in = $in;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $in= $this->in;
        return $this->markdown('email.colo',['in' => $in])->subject(   encription::decryptdata($in['username']).' | Safelock Withdraw |'.'Reno');
    }
}
