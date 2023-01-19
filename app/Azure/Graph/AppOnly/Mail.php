<?php

namespace App\Azure\Graph\AppOnly;
use App\Azure\Graph\Auth\Authentication;
use Illuminate\Support\Facades\Http;

class Mail{

    public function mailSend($subject, $content, $recipients)
    {
        $msGraphAuth = new Authentication();
        $msGraphSignInResponse = $msGraphAuth->signIn();

        foreach ($recipients as $key => $recipient){
            $formatRecipients[] = [
                "emailAddress"=> [
                    "address"=> $recipient
                ]
            ];
        }

        $accessToken =  $msGraphSignInResponse->access_token;
            $request = Http::acceptJson()
                ->withToken($accessToken)
                ->withBody(json_encode($this->mailTemplate($subject, $content, $formatRecipients)), 'application/json')
                ->post(config("azure.msgraphUrl")."/users/".config("azure.userId")."/sendMail");
        $responseCode = json_decode($request->status());

        return $responseCode;


    }

    private function mailTemplate($subject, $content, $recipients){
        $year = date("Y");
        return [
            "message"=>[
                "subject"=> "$subject",
                "body"=> [
                  "contentType"=> "HTML",
                  "content"=>
                  "<!DOCTYPE html>
                  <html>
                    <head>
                    </head>
                  <body>
                    {$content}
                    <p>PT Suryacipta Swadaya - IT Division &#169; {$year} </p>
                  </body>
                  </html>
                  "
                ],
                "toRecipients"=> $recipients//,
                // "ccRecipients"=> [
                //     [
                //         "emailAddress"=> [
                //         "address"=> "eryan.fauzan@suryacipta.com"
                //         ]
                //     ]
                // ]
            ],
              "saveToSentItems"=> "true"
        ];
    }

}
