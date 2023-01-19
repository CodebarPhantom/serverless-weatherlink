<?php

namespace App\Http\Controllers\API\V1\TenantRelation;

use App\Http\Controllers\ApiController;
use App\Azure\Graph\Auth\Authentication;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MaintenanceSchedule extends ApiController
{
    public function createCalendarEvent()
    {
        $msGraphAuth = new Authentication();
        $msGraphSignInResponse = $msGraphAuth->signIn();
    }
}
