<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Emailpass extends Mailable
{
    use Queueable, SerializesModels;
    protected $new;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($new)
    {
        $this->new = $new;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $new= $this->new;
        return $this->markdown('email.pass',['new' => $new])->subject(  'New Password');
    }
}
