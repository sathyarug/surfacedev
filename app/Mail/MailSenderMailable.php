<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Contracts\Queue\ShouldQueue;

class MailSenderMailable extends Mailable /*implements ShouldQueue*/
{
    use Queueable, SerializesModels;

     public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($type, $data, $subject)
    {
        $this->data = $data;
        $this->type = $type;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->type == 'RESET_ACCOUNT'){
            return $this->subject($this->subject)->view('email.reset_account')->with($this->data);
        }
        else{
            return $this->view('email.email')->with($this->data);
        }
    }
}
