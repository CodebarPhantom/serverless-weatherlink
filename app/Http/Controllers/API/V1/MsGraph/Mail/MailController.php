<?php

namespace App\Http\Controllers\API\V1\MsGraph\Mail;

use App\Http\Controllers\ApiController;
use App\Azure\Graph\AppOnly\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MailController extends ApiController
{
    public function send(Request $request)
    {
        $func = function () use ($request){

            $mail = new Mail();
            $subject = $request->subject;
            $content = $request->content;
            $recipients = $request->recipients;
            $msGraphSignInResponse = $mail->mailSend($subject, $content, $recipients);

            $this->data = $msGraphSignInResponse;
        };

        return $this->callFunction($func);
    }


}
