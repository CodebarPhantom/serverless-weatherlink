<?php

namespace App\Azure\Graph\AppOnly;

use App\Azure\Graph\Auth\Authentication;
use App\Azure\Graph\Helpers\Formatter;
use App\Azure\Graph\Helpers\TimeZones;
use Illuminate\Support\Facades\Http;


class Calendar{

    public function createEvent($subject, $content, $startDateTime, $endDateTime, $location, $attendees, $attendeedType, $isOnlineMeeting, $transactionUuid){

        $msGraphAuth = new Authentication();
        $msGraphSignInResponse = $msGraphAuth->signIn();

        $msGraphFormatter = new Formatter();
        $attendeeFormat = $msGraphFormatter->attendees($attendees,$attendeedType);

        $accessToken =  $msGraphSignInResponse->access_token;

        $response = Http::acceptJson()
            ->withHeaders([
                'Prefer' => 'outlook.timezone="SE Asia Standard Time"'
            ])
            ->withToken($accessToken)
            ->withBody(json_encode($msGraphFormatter->createEvent($subject, $content, $startDateTime, $endDateTime, $location, $attendeeFormat, $isOnlineMeeting, $transactionUuid)), 'application/json')
            ->post(config("azure.msgraphUrl")."/users/".config("azure.userId")."/calendar/events");

        //dd($response);
        // $response = json_decode($request->status());
        return json_decode($response);

    }
}
