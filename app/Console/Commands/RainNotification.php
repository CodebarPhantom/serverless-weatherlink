<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\MasterConfig;
use App\Models\MasterEmailSend;
use App\Models\WeatherHistory;
use Illuminate\Support\Facades\Log;

class RainNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ms-graph:rain-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email with microsoft graph if start rain';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        //$currentUnixEpochTime = $now->copy()->timestamp;
        //$expiredTimestampMsGraph =  MasterConfig::whereId('EXPIRED_TIMESTAMP_MSGRAPH')->first()->value;

        // if ($currentUnixEpochTime > $expiredTimestampMsGraph && RAIN_RATE_LIMIT) {
        //$accessToken = $this->signin()->access_token;
        //MasterConfig::whereId('EXPIRED_TIMESTAMP_MSGRAPH')->update(['value' => $currentUnixEpochTime += $this->signin()->expires_in]);
        //}


        // //dd(json_encode($test));
        // $accessToken = $this->signin()->access_token;
        // $request = Http::acceptJson()
        //     ->withToken($accessToken)
        //     // withHeaders([
        //     //     'Authorization' =>  'Bearer '.$accessToken,
        //     //     'Content-Type' =>  "application/json"
        //     // ])
        //     ->withBody(json_encode($this->arrayTemplateEmail()), 'application/json')
        //     ->post("https://graph.microsoft.com/v1.0/users/efe38c7b-3f34-4ede-afce-63f84b4487ee/sendMail");

        //   return $response = json_decode($request->status());



        $lastRainRate = WeatherHistory::where('master_station_id',140323)->where('is_send', false)->orderBy('unix_epoch_time', 'desc')->first()->rain_rate_hi_mm ?? 0;
        $rainTreshold = MasterConfig::whereId('RAIN_RATE_THRESHOLD')->first()->value;
        if ($lastRainRate >= $rainTreshold) {

            $dataRainRate = WeatherHistory::where('master_station_id',140323)
                ->where('is_send', false)
                ->orderBy('unix_epoch_time', 'desc')
                ->first();
            if ($dataRainRate) { // Ensure a record exists before updating
                $dataRainRate->is_send = true;
                $dataRainRate->save();
            } else {
                // Optional: Log or handle the case where no record is found
                Log::info('No unsent WeatherHistory records found.');
            }

            $getEmailRecipients = MasterEmailSend::whereIsTo(true)->whereIsActive(true)->whereSendKarawang(true)->get();
            $formatRecipients = [];

            foreach ($getEmailRecipients as $getEmailRecipient) {
                $formatRecipients[] = [
                    "emailAddress" => [
                        "address" => $getEmailRecipient->email
                    ]
                ];
            }

            $getLastRainRateThirtyMinutesSum =  WeatherHistory::where('master_station_id',140323)->orderBy('unix_epoch_time', 'desc')->limit(6)->get()->sum('rain_rate_hi_mm');
            $averageRainRate = number_format((float)$getLastRainRateThirtyMinutesSum / 6, 2, '.', ',');


            $accessToken = $this->signin()->access_token;
            $request = Http::acceptJson()
                ->withToken($accessToken)
                ->withBody(json_encode($this->arrayTemplateEmail($lastRainRate, $averageRainRate, $now->locale('id')->translatedFormat("D, d F Y H:i"), $formatRecipients)), 'application/json')
                ->post(config("azure.msgraphUrl") . "/users/" . config("azure.userId") . "/sendMail");


            //$response = json_decode($request->status());
        }
        return;
    }

    private function signin()
    {
        $request = Http::asForm()->post(config("azure.authority") . config("azure.tokenEndpoint"), [
            'client_id' => config("azure.appId"),
            'scope' => config("azure.scopes"),
            'client_secret' => config("azure.appSecret"),
            'grant_type' => 'client_credentials',

        ]);
        $response = json_decode($request->getBody());

        return $response;
    }

    private function arrayTemplateEmail($lastRainRate, $avereageRainRate, $dateTime, $formatRecipients)
    {
        $year = date("Y");
        return [
            "message" => [
                "subject" => "Informasi Curah Hujan di Kawasan Suryacipta",
                "body" => [
                    "contentType" => "HTML",
                    "content" =>
                    "<!DOCTYPE html>
                  <html>
                    <head>
                    </head>
                  <body>
                    <p>
                        Halo Estate Team,  <br/>
                        Pesan otomatis ini dikirimkan oleh system untuk memberitahukan informasi curah hujan tinggi beserta rata-rata curah hujan 30 menit terakhir pada kawasan Suryacipta:
                    </p>
                    <p>
                        Curah hujan terakhir: <b>{$lastRainRate} mm/hr</b> <br/>
                        Rata-rata curah hujan 30 menit terakhir: <b>{$avereageRainRate} mm/hr</b><br/>
                        Waktu: $dateTime
                    </p>

                    <p>
                        Dashboard dapat diakses pada <a href='https://weather.suryacipta.com/'>https://weather.suryacipta.com/</a>
                    </p>
                    <p>PT Suryacipta Swadaya - IT Division &#169; {$year} </p>
                  </body>
                  </html>
                  "
                ],
                "toRecipients" => $formatRecipients //,
                // "ccRecipients"=> [
                //     [
                //         "emailAddress"=> [
                //         "address"=> "eryan.fauzan@suryacipta.com"
                //         ]
                //     ]
                // ]
            ],
            "saveToSentItems" => "false"
        ];
    }
}
