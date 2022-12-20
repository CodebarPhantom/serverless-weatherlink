<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Microsoft\Graph\Graph;
use microsoft\Graph\Model;
use App\TokenStore\TokenCache;
use Carbon\Carbon;
use App\Models\MasterConfig;

class TestAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$now = Carbon::now();
        //$currentUnixEpochTime = $now->copy()->timestamp;
        //$expiredTimestampMsGraph =  MasterConfig::whereId('EXPIRED_TIMESTAMP_MSGRAPH')->first()->value;

       // if ($currentUnixEpochTime > $expiredTimestampMsGraph) {
            $accessToken = $this->signin()->access_token;
            //MasterConfig::whereId('EXPIRED_TIMESTAMP_MSGRAPH')->update(['value' => $currentUnixEpochTime += $this->signin()->expires_in]);
        //}



        //dd(json_encode($test));

        $request = Http::acceptJson()
            ->withToken($accessToken)
            // withHeaders([
            //     'Authorization' =>  'Bearer '.$accessToken,
            //     'Content-Type' =>  "application/json"
            // ])
            ->withBody(json_encode($this->arrayTemplateEmail()), 'application/json')
            ->post("https://graph.microsoft.com/v1.0/users/efe38c7b-3f34-4ede-afce-63f84b4487ee/sendMail");

        $response = json_decode($request->status());
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

    private function arrayTemplateEmail(){
        return [
            "message"=>[
                "subject"=> "Average rainfall last 30 minutes in Suryacipta",
                "body"=> [
                  "contentType"=> "HTML",
                  "content"=> "<b>The new cafeteria is open :v sip</b>"
                ],
                "toRecipients"=> [
                  [
                    "emailAddress"=> [
                      "address"=> "eryan.fauzan@suryacipta.com"
                    ]
                  ]
                ]//,
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
