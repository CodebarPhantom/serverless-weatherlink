<?php

namespace App\Http\Controllers\API\V1\MsGraph\Calendar;

use App\Http\Controllers\ApiController;
use App\Azure\Graph\AppOnly\Calendar;
use Illuminate\Http\Request;
//use Carbon\Carbon;
use Illuminate\Support\Str;

class CalendarController extends ApiController
{
    public function createEvent(Request $request){

        $func = function () use ($request){

            $calendar = new Calendar();
            $subject = $request->subject;
            $content = $request->content;
            $startDateTime = "{$request->start_date}T{$request->start_time}";
            $endDateTime = "{$request->end_date}T{$request->end_time}";
            $location = $request->location;
            $attendees = $request->attendees;
            $attendeedType = $request->is_attendees_required;

            $isOnlineMeeting = $request->is_online_meeting;

            $transactionUuid = Str::uuid();
            $response_create_event = $calendar->createEvent($subject, $content, $startDateTime, $endDateTime, $location, $attendees, $attendeedType, $isOnlineMeeting, $transactionUuid);

            $this->data = $response_create_event;
        };

        return $this->callFunction($func);
    }
}
