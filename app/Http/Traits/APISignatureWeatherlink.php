<?php
namespace App\Http\Traits;
use Illuminate\Support\Facades\Http;


trait APISignatureWeatherlink {

    public function currentWeatherHMAC($stasionId,$currentUnixEpochTime) {
        return hash_hmac('sha256', 'api-key'.env('WEATHERLINK_API_KEY').'station-id'.$stasionId.'t'.$currentUnixEpochTime, env('WEATHERLINK_API_SECRET'));
    }

    public function historicWeatherHMAC($stasionId,$currentUnixEpochTime,$startTime,$endTime)
    {
        return hash_hmac('sha256','api-key'.env('WEATHERLINK_API_KEY').'end-timestamp'.$endTime.'start-timestamp'.$startTime.'station-id'.$stasionId.'t'.$currentUnixEpochTime,env('WEATHERLINK_API_SECRET'));
    }

    private function getDataFromAPI($stasionID, $currentUnixEpochTime, $startTime, $endTime)
    {
        $request = Http::get(env('WEATHERLINK_URL')."/historic/{$stasionID}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&start-timestamp={$startTime}&end-timestamp={$endTime}&api-signature={$this->historicWeatherHMAC($stasionID,$currentUnixEpochTime,$startTime,$endTime)}");
        $response = json_decode($request->getBody());

        return $response;

    }
}
