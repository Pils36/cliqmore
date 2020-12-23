<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Mail;
//Mail
use App\Mail\sendMail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $subject;
    public $message;
    public $to = "app@cliqmore.com";
    public $file = "";
    public $url;
    public $curldata;

    // Response
    public function returnJSON($data, $status){
        return response($data, $status)->header('Content-Type', 'application/json');
    }



    // Mail Sender
    public function sendMail($objDemoa, $purpose){
        $objDemo = new \stdClass();
        $objDemo->purpose = $purpose;

            if($purpose == $this->subject){
                $objDemo->subject = $this->subject;
                $objDemo->message = $this->message;
                $objDemo->file = $this->file;
            }

            Mail::to($objDemoa)
                ->send(new sendMail($objDemo));
    }

}
