<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;

use Illuminate\Support\Facades\Mail;
use App\Mail\MailSenderMailable;
use App\Jobs\MailSendJob;

class TestController extends Controller {

    public function index() {

    }

    public function auth(Request $request) {
        return json_encode(array(
          'status' => 'success',
          'message' => 'You successfully loged In'
        ));
    }


    public function send_mail(){
      $data = [
        'to' => [['email' => 'chamilap@helaclothing.com'],['email' => 'chamilamacp@gmail.com']],
        'mail_data' => [
          'header_title' => 'Test',
          'body' => 'This is a sample email'
        ]
      ];
      $job = new MailSendJob($data);
      dispatch($job);
    //  MailSendJob::dispatch();
    //return view('email.email');
    }




}
