<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetDataFromWeatherlinkAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weatherlink-api:get-stasion-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weatherlink API GET Stasion ID';

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

        //$request = Http::get('https://api.weatherlink.com/v2/historic/140323?api-key='.env('WEATHERLINK_API_KEY').'t=1666339200&start-timestamp=1666335600&end-timestamp=1666338600'.'&api-signature='.hash_hmac('sha256', 'api-key'.env('WEATHERLINK_API_KEY').'end-timestamp1666338600start-timestamp1666335600station-id140323'.'t1666339200', env('WEATHERLINK_API_SECRET')));
        $request = Http::get('https://api.weatherlink.com/v2/report/et/140323 ?api-key=grejkxsbo6g3r8rigf8vmzcpc7rkmhl2&t=1666340074&api-signature=a0aa066473e863da812a8eec0233fdbbf05f1868962b512abdf9942288337fbd');
        $response = json_decode($request->getBody());
        dd($response);
    }
}
