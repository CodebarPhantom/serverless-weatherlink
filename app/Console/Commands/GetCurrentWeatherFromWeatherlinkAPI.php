<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Traits\APISignatureWeatherlink;
use Carbon\Carbon;
use App\Models\WeatherHistory;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\MasterConfig;

class GetCurrentWeatherFromWeatherlinkAPI extends Command
{
    use APISignatureWeatherlink;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weatherlink-api:current-weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weatherlink API GET Current Weather';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $suryaciptaStasion = 140323;
        $now = Carbon::now();
        $currentUnixEpochTime = $now->copy()->timestamp;
        $pastUnixEpochTime = $now->copy()->subMinutes(6)->timestamp;

        $startTime = $pastUnixEpochTime; //for historic period
        $endTime = $currentUnixEpochTime ;
        $request = Http::get(env('WEATHERLINK_URL')."/historic/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&start-timestamp={$startTime}&end-timestamp={$endTime}&api-signature={$this->historicWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime,$startTime,$endTime)}");
        //$request = Http::get(env('WEATHERLINK_URL')."/current/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&api-signature={$this->currentWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime)}"); //for current
        $response = json_decode($request->getBody());
        dd($response->sensors[0]);

        DB::beginTransaction();
        try {


            if($response->sensors){
                foreach ($response->sensors as $dataWeahterHistories) {
                    $weatherHistory = new WeatherHistory();
                    $weatherHistory->master_stasion_id = $suryaciptaStasion;
                    $weatherHistory->unix_epoch_time = $dataWeahterHistories->data[0]->ts;
                    $weatherHistory->rain_rate_hi_mm = $dataWeahterHistories->data[0]->rain_rate_hi_mm;
                    $weatherHistory->save();

                    $unixEpochTimeTemp = $weatherHistory->unix_epoch_time; // get last timestamp
                }

                MasterConfig::whereId('LAST_UPDATE_API_TIME')->update(['value' => $unixEpochTimeTemp]);


            }else{
                Log::error('Error Tidak mendapatkan data 5 Menit terakhir dari API Weatherlink !!');
            }

        } catch (Exception $e) {
            DB::rollBack();
            report($e);
        }
        DB::commit();
    }
}
