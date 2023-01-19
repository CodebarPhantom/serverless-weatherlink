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

            $this->validate($request, [
                "subject" => ["required"],
                "content" => ["required"],
                "recipients" => ["required"],
            ]);

            $mail = new Mail();
            $subject = $request->subject;
            $content = $request->content;
            $recipients = $request->recipients;
            $response_code = $mail->mailSend($subject, $content, $recipients);

            $this->data = $response_code;
        };

        return $this->callFunction($func);
    }


}
