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
use Exception;
use Throwable;

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
        $pastUnixEpochTime = $now->copy()->subMinutes(11)->timestamp;

        $startTime = $pastUnixEpochTime; //for historic period
        $endTime = $currentUnixEpochTime ;
        //$request = Http::get(env('WEATHERLINK_URL')."/current/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&api-signature={$this->currentWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime)}"); //for current
        //$request = Http::get("https://api.weatherlink.com/v2/historic/140323?api-key=grejkxsbo6g3r8rigf8vmzcpc7rkmhl2&t=1671088043&start-timestamp=1671009000&end-timestamp=1671095400&api-signature=0aafe55b22dc67526a53482c6ccee0b0317641d235a1c9fc7647c28ac324d1ba");
        $weatherlinkData = $this->getDataFromAPI($suryaciptaStasion,$currentUnixEpochTime, $startTime,$endTime)->sensors[0]; // langsusng ambil ke sensor 0
        // /dd($weatherlinkData);

        DB::beginTransaction();
        try {

            if(!empty($weatherlinkData->data)){

                $lastTimestampDB = (int)MasterConfig::whereId('LAST_TIMESTAMP_WEATHERLINK')->first()->value;
                $lastTimestampWeatherlink = $weatherlinkData->data[0]->ts;

                if ($lastTimestampWeatherlink !== $lastTimestampDB){

                    $tempLastTimestampDB = $lastTimestampDB;

                    while( $tempLastTimestampDB < $endTime){
                        $weatherlinkDataBatch = $this->getDataFromAPI($suryaciptaStasion, $currentUnixEpochTime, $tempLastTimestampDB, $endTime)->sensors[0]; //get lagi data ke API dengan parameter baru dan langsung ke sensor 0

                        foreach ($weatherlinkDataBatch->data as $data) {
                            $this->newWeatherHistory(
                                $suryaciptaStasion,
                                $data->ts,
                                $data->rain_rate_hi_mm,
                                $data->rainfall_mm
                            );
                            $tempUpdateTimestamp = $data->ts;
                        }

                        $tempLastTimestampDB += 86400; //langsung tambah 24jam wkwkwk
                    }
                    MasterConfig::whereId('LAST_TIMESTAMP_WEATHERLINK')->update(['value' => $tempUpdateTimestamp]);
                }else{
                    $this->newWeatherHistory(
                        $suryaciptaStasion,
                        $weatherlinkData->data[1]->ts,
                        $weatherlinkData->data[1]->rain_rate_hi_mm,
                        $weatherlinkData->data[1]->rainfall_mm
                    );
                    MasterConfig::whereId('LAST_TIMESTAMP_WEATHERLINK')->update(['value' => $weatherlinkData->data[1]->ts]);
                }
            }else{
                throw new Exception('Error Tidak mendapatkan data 5 Menit terakhir dari API Weatherlink !!');
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    private function getDataFromAPI($stasionID, $currentUnixEpochTime, $startTime, $endTime)
    {
        $request = Http::get(env('WEATHERLINK_URL')."/historic/{$stasionID}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&start-timestamp={$startTime}&end-timestamp={$endTime}&api-signature={$this->historicWeatherHMAC($stasionID,$currentUnixEpochTime,$startTime,$endTime)}");
        $response = json_decode($request->getBody());

        return $response;

    }

    private function newWeatherHistory($stasionID, $timestamp, $rainRateHiMM, $rainRate)
    {
        $weatherHistory = new WeatherHistory();
        $weatherHistory->master_stasion_id = $stasionID;
        $weatherHistory->unix_epoch_time = $timestamp;
        $weatherHistory->rain_rate_hi_mm = $rainRateHiMM;
        $weatherHistory->rain_rate = $rainRate;
        $weatherHistory->save();

        return;
    }
}
