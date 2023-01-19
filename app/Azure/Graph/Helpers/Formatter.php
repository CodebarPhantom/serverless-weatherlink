<?php

namespace App\Azure\Graph\Helpers;

class Formatter
{
    public function recipients($recipients){

        foreach ($recipients as $key => $recipient){
            $recipientsFormat[] = [
                "emailAddress"=> [
                    "address"=> $recipient
                ]
            ];
        }

        return $recipientsFormat;
    }

    public function attendees($attendees,$attendeedType){
        foreach ($attendees as $at => $attendee){
            $attendeesFormat[] = [
                "emailAddress"=> [
                    "address"=> $attendee,
                    //"name"=>$attendee,
                ],
                "type"=> $attendeedType[$at]
            ];
        }

        return $attendeesFormat;
    }

    public function createEvent($subject, $content, $startDateTime, $endDateTime, $location, $attendees, $isOnlineMeeting, $transactionUuid){
        return [
                "subject" => "$subject",
                "body"=> [
                    "contentType" => "HTML",
                    "content" => "$content"
                ],
                "start"=>[
                    "dateTime"=>"$startDateTime",
                    "timeZone"=>"SE Asia Standard Time"
                ],
                "end"=>[
                    "dateTime"=>"$endDateTime",
                    "timeZone"=>"SE Asia Standard Time"
                ],
                "location"=>[
                    "displayName"=>$location
                ],
                "attendees"=>$attendees,
                "allowNewTimeProposals"=>true,
                "isOnlineMeeting"=>$isOnlineMeeting,
                "onlineMeetingProvider"=>"teamsForBusiness",
                "transactionId"=>"$transactionUuid"
            ];
    }

    public function mail($subject, $content, $recipients){
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
