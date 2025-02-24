<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Traits\APISignatureWeatherlink;
use Carbon\Carbon;
use App\Models\WeatherHistory;
use DB;
use App\Models\MasterConfig;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\MasterStation;

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
        set_time_limit(0);

        $stations = MasterStation::all();
        $apiCallsThisHour = 0; // Track API calls in the current hour
        $startTimeHour = time(); // Track the start time of the current hour
        $apiCallsSinceLastSleep = 0; // Track API calls since the last sleep
        //Log::debug("Mulai: ".Carbon::now()->format('Y-m-d H:i:s'));
        foreach ($stations as $station) {
            $stationId = $station->id;
            $lastTimestampDB = $station->last_timestamp ?? 0;

            $now = Carbon::now();
            $currentUnixEpochTime = $now->copy()->timestamp;
            $pastUnixEpochTime = $now->copy()->subMinutes(13)->timestamp;

            $startTime = $pastUnixEpochTime; // For historic period
            $endTime = $currentUnixEpochTime;

            // Check if the hourly API call limit is reached
            if ($apiCallsThisHour >= 1000) {
                $timeElapsed = time() - $startTimeHour;
                if ($timeElapsed < 3600) {
                    // Wait until the hour is over
                    $sleepTime = 3600 - $timeElapsed;
                    //Log::info("Hourly API call limit reached. Sleeping for {$sleepTime} seconds.");
                    sleep($sleepTime);
                }
                // Reset the counter and start time
                $apiCallsThisHour = 0;
                $startTimeHour = time();
            }

            // Sleep for 1 minute after every 10 API calls
            if ($apiCallsSinceLastSleep >= 10) {
                //Log::info("10 API calls made. Sleeping for 60 seconds.");
                sleep(60); // Sleep for 1 minute
                $apiCallsSinceLastSleep = 0; // Reset the counter
            }

            $request = Http::withHeaders([
                'X-Api-Secret' => env('WEATHERLINK_API_SECRET')
            ])->get(env('WEATHERLINK_URL') . "/historic/{$stationId}", [
                'api-key' => env('WEATHERLINK_API_KEY'),
                'station-id' => $stationId,
                'start-timestamp' => $startTime,
                'end-timestamp' => $endTime
            ]);

            $apiCallsThisHour++; // Increment the API call counter
            $apiCallsSinceLastSleep++; // Increment the counter for sleep
            //Log::info("API calls this hour: {$apiCallsThisHour}");
            //Log::info("API calls since last sleep: {$apiCallsSinceLastSleep}");

            // Sleep for 100ms to respect the 10 calls per second limit
            usleep(100000); // 100ms delay

            $weatherlinkData = json_decode($request->getBody());

            if (isset($weatherlinkData->sensors) && !empty($weatherlinkData->sensors)) {
                $sensorData = $weatherlinkData->sensors[0];
                $lastTimestampWeatherlink = $sensorData->data[0]->ts;

                if ($lastTimestampWeatherlink !== $lastTimestampDB) {
                    $dataFound = false; // Flag to track if any data is found

                    // Use a for loop to iterate through the time range
                    for (
                        $tempLastTimestampDB = $lastTimestampDB, $maxTime = $tempLastTimestampDB + 86400;
                        $tempLastTimestampDB < $endTime;
                        $tempLastTimestampDB += 86400, $maxTime += 86400
                    ) {
                        //Log::info("Processing time range: {$tempLastTimestampDB} to {$maxTime} for station {$stationId}");

                        // Check if the hourly API call limit is reached
                        if ($apiCallsThisHour >= 1000) {
                            $timeElapsed = time() - $startTimeHour;
                            if ($timeElapsed < 3600) {
                                // Wait until the hour is over
                                $sleepTime = 3600 - $timeElapsed;
                                //Log::info("Hourly API call limit reached. Sleeping for {$sleepTime} seconds.");
                                sleep($sleepTime);
                            }
                            // Reset the counter and start time
                            $apiCallsThisHour = 0;
                            $startTimeHour = time();
                        }

                        // Sleep for 1 minute after every 10 API calls
                        if ($apiCallsSinceLastSleep >= 10) {
                            //Log::info("10 API calls made. Sleeping for 60 seconds.");
                            sleep(60); // Sleep for 1 minute
                            $apiCallsSinceLastSleep = 0; // Reset the counter
                        }

                        $weatherlinkDataBatch = $this->getDataFromAPI($stationId, $currentUnixEpochTime, $tempLastTimestampDB, $maxTime);
                        $apiCallsThisHour++; // Increment the API call counter
                        $apiCallsSinceLastSleep++; // Increment the counter for sleep
                        //Log::info("API calls this hour: {$apiCallsThisHour}");
                        //Log::info("API calls since last sleep: {$apiCallsSinceLastSleep}");

                        // Sleep for 100ms to respect the 10 calls per second limit
                        usleep(100000); // 100ms delay

                        if (isset($weatherlinkDataBatch->sensors) && !empty($weatherlinkDataBatch->sensors)) {
                            $batchSensorData = $weatherlinkDataBatch->sensors[0];

                            foreach ($batchSensorData->data as $data) {
                                $this->newWeatherHistory(
                                    $stationId,
                                    $data->ts,
                                    $data->rain_rate_hi_mm,
                                    $data->rainfall_mm
                                );
                                $tempUpdateTimestamp = $data->ts;
                                $dataFound = true; // Data was found
                            }
                        } else {
                            //Log::warning("No sensor data found for station {$stationId} in the specified time range.");
                            //Log::debug("Selesai: ".Carbon::now()->format('Y-m-d H:i:s'));

                            break; // Early exit if no data is found in this batch
                        }
                    }

                    // Update the last_timestamp only if new data was found
                    if ($dataFound) {
                        $station->last_timestamp = $tempUpdateTimestamp;
                        $station->save();
                    }
                } else {
                    $this->newWeatherHistory(
                        $stationId,
                        $sensorData->data[1]->ts,
                        $sensorData->data[1]->rain_rate_hi_mm,
                        $sensorData->data[1]->rainfall_mm
                    );

                    // Update the last_timestamp for the station
                    $station->last_timestamp = $sensorData->data[1]->ts;
                    $station->save();
                }
            } else {
                //Log::warning("No sensor data found for station {$stationId} in the last 5 minutes from Weatherlink API.");
            }
        }
    }

    private function newWeatherHistory($stasionID, $timestamp, $rainRateHiMM, $rainRate)
    {
        $weatherHistory = new WeatherHistory();
        $weatherHistory->master_station_id = $stasionID;
        $weatherHistory->unix_epoch_time = $timestamp;
        $weatherHistory->rain_rate_hi_mm = $rainRateHiMM;
        $weatherHistory->rain_rate = $rainRate;
        $weatherHistory->save();

        return;
    }
}
