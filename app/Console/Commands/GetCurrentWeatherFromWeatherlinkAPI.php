<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Traits\APISignatureWeatherlink;
use Carbon\Carbon;
use App\Models\WeatherHistory;
use DB;

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
        $currentUnixEpochTime = Carbon::now()->timestamp;
        // $startTime =1665565200; for historic period
        // $endTime =1665568800;
        // $request = Http::get(env('WEATHERLINK_URL')."/historic/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&start-timestamp={$startTime}&end-timestamp={$endTime}&api-signature={$this->historicWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime,$startTime,$endTime)}");
        $request = Http::get(env('WEATHERLINK_URL')."/current/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&api-signature={$this->currentWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime)}");
        $response = json_decode($request->getBody());

        DB::beginTransaction();
        try {

            $weatherHistory = new WeatherHistory();
            $weatherHistory->master_stasion_id = $suryaciptaStasion;
            $weatherHistory->unix_epoch_time = $response->sensors[0]->data[0]->ts;
            $weatherHistory->rain_rate_hi_mm = $response->sensors[0]->data[0]->rain_rate_mm;
            $weatherHistory->save();

        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return abort(500);
        }
        DB::commit();
    }
}