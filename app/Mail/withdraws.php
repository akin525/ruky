<?php

namespace App\Mail;

use App\Console\encription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class withdraws extends Mailable
{
    use Queueable, SerializesModels;
    protected $insert;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($insert)
    {
        $this->insert = $insert;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $insert= $this->insert;
        return $this->markdown('email.withdraw',['insert' => $insert])->subject(   encription::decryptdata($insert['username']).' |Withdraw Notification|');
    }
}
