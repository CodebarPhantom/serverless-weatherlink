<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Microsoft\Graph\Graph;
use microsoft\Graph\Model;
use App\TokenStore\TokenCache;
use Carbon\Carbon;
use App\Models\MasterConfig;
use App\Models\MasterEmailSend;
use App\Models\WeatherHistory;

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



        $lastRainRate = WeatherHistory::orderBy('unix_epoch_time','desc')->first()->rain_rate_hi_mm;
        if($lastRainRate > 0){

            $getEmailRecipients = MasterEmailSend::whereIsTo(true)->whereIsActive(1)->get();
            $formatRecipients = [];
            foreach ($getEmailRecipients as $getEmailRecipient){

                $formatRecipients[] = [
                    "emailAddress"=> [
                        "address"=> $getEmailRecipient->email
                    ]
                ];
            }

            $getLastRainRateThirtyMinutesSum =  WeatherHistory::orderBy('unix_epoch_time','desc')->limit(6)->get()->sum('rain_rate_hi_mm');
            $averageRainRate = number_format((float)$getLastRainRateThirtyMinutesSum / 6, 2, '.', ',');


            $accessToken = $this->signin()->access_token;
            $request = Http::acceptJson()
                ->withToken($accessToken)
                ->withBody(json_encode($this->arrayTemplateEmail($lastRainRate, $averageRainRate,$now->format("D, d F Y H:i"),$formatRecipients)), 'application/json')
                ->post(config("azure.msgraphUrl")."/users/".config("azure.userId")."/sendMail");


            $response = json_decode($request->status());
        }
        return;
    }

    private function signin()
    {
        $request = Http::asForm()->post(config("azure.authority").config("azure.tokenEndpoint"), [
            'client_id' => config("azure.appId"),
            'scope' => config("azure.scopes"),
            'client_secret' => config("azure.appSecret"),
            'grant_type' => 'client_credentials',

        ]);
        $response = json_decode($request->getBody());

        return $response;
    }

    private function arrayTemplateEmail($lastRainRate, $avereageRainRate, $dateTime, $formatRecipients){
        $year = date("Y");
        return [
            "message"=>[
                "subject"=> "Rata-rata Curah Hujan 30 Menit Terakhir di Kawasan Suryacipta",
                "body"=> [
                  "contentType"=> "HTML",
                  "content"=>
                  "<!DOCTYPE html>
                  <html>
                    <head>
                    </head>
                  <body>
                    <p>
                        Halo, <br/><br/>
                        Pesan otomatis ini di hasilkan oleh IT Intelligence API untuk memberitahukan rata-rata curah hujan 30 menit terakhir pada kawasan Suryacipta berikut adalah rincinannya:
                    </p>
                    <p>
                        Curah hujan terakhir: <b>{$lastRainRate} mm/hr</b> <br/>
                        Rata-rata curah hujan 30 menit terakhir: <b>{$avereageRainRate} mm/hr</b><br/>
                        Timestamp: $dateTime
                    </p>
                    <p>PT Suryacipta Swadaya - IT Division &#169; {$year} </p>
                  </body>
                  </html>
                  "
                ],
                "toRecipients"=> $formatRecipients//,
                // "ccRecipients"=> [
                //     [
                //         "emailAddress"=> [
                //         "address"=> "eryan.fauzan@suryacipta.com"
                //         ]
                //     ]
                // ]
            ],
              "saveToSentItems"=> "false"
        ];



    }
}
