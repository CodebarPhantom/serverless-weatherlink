<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\ApiController;
use App\Http\Traits\APISignatureWeatherlink;
use Illuminate\Http\Request;
use Carbon\Carbon;
//use DB, Exception;

class WeatherlinkController extends ApiController
{
    use APISignatureWeatherlink;

    public function historic(Request $request){

        $func = function () use ($request){

            $this->validate($request, [
                "stasion_id" => ["required"],
                "unix_start_time" => ["required"],
                "unix_end_time" => ["required"],
            ]);


            $stasionId = $request->stasion_id;
            $now = Carbon::now()->timestamp;
            //$currentUnixEpochTime = $request->unix_start_time;
            //$pastUnixEpochTime = $request->unix_end_time;

            $startTime = $request->unix_start_time; //for historic period
            $endTime = $request->unix_end_time;
            //$request = Http::get(env('WEATHERLINK_URL')."/current/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&api-signature={$this->currentWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime)}"); //for current
            //$request = Http::get("https://api.weatherlink.com/v2/historic/140323?api-key=grejkxsbo6g3r8rigf8vmzcpc7rkmhl2&t=1671088043&start-timestamp=1671009000&end-timestamp=1671095400&api-signature=0aafe55b22dc67526a53482c6ccee0b0317641d235a1c9fc7647c28ac324d1ba");
            $datas = $this->getDataFromAPI($stasionId, $now, $startTime, $endTime)->sensors[0]; // langsusng ambil ke sensor 0


            $this->data = $datas;
        };

        return $this->callFunction($func);
    }

}
