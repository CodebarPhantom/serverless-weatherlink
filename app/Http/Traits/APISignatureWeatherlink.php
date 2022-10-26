<?php
namespace App\Http\Traits;
use App\Models\MasterStasion;

trait APISignatureWeatherlink {

    public function currentWeatherHMAC($stasionId,$currentUnixEpochTime) {
        return hash_hmac('sha256', 'api-key'.env('WEATHERLINK_API_KEY').'station-id'.$stasionId.'t'.$currentUnixEpochTime, env('WEATHERLINK_API_SECRET'));
    }

    public function historicWeatherHMAC($stasionId,$currentUnixEpochTime,$startTime,$endTime)
    {
        return hash_hmac('sha256','api-key'.env('WEATHERLINK_API_KEY').'end-timestamp'.$endTime.'start-timestamp'.$startTime.'station-id'.$stasionId.'t'.$currentUnixEpochTime,env('WEATHERLINK_API_SECRET'));
    }
}
