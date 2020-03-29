<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Mail;
use App\Mail\ApprovalMailable;

class ApprovalMailSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data = null;
    private $to = [];
    private $cc = [];
    private $bcc = [];
    private $process = null;
    private $subject = null;

    public function __construct($_process, $_subject, $_data = [], $_to = [], $_cc = [], $_bcc = [])
    {
        $this->process = $_process;
        $this->subject = $_subject;
        $this->data = $_data;
        $this->to = $_to;
        $this->cc = $_cc;
        $this->bcc = $_bcc;
    }


    public function handle()
    {
      Mail::to($this->to)->send(new ApprovalMailable($this->process, $this->data, $this->subject));
    }
}
