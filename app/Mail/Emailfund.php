<?php

namespace App\Mail;

use App\Console\encription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Emailfund extends Mailable
{
    use Queueable, SerializesModels;
    protected $deposit;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $deposit= $this->deposit;
        return $this->markdown('emails.fund',['deposit' => $deposit])->subject(   $deposit['username'].' |Account Funded |'.''. $deposit['refid']);
    }
}
