<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Models\WeatherHistoryReport;
use Yajra\DataTables\DataTables;
use App\Http\Traits\APISignatureWeatherlink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;



class WeatherHistoryReportController extends Controller
{
    use APISignatureWeatherlink;

    public function index()
    {
        return view('weather-history.report');
    }

    public function data()
    {

        $reports = WeatherHistoryReport::orderBy('created_at','desc');

        return DataTables::of($reports)
        ->editColumn('path_s3', function ($report) {
            $url = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/';
            return '<strong><a href="'.$url.$report->path_s3.'" style="text-decoration: none;">Download</a></strong>';
        })
        ->rawColumns(['path_s3'])
        ->make(true);
    }

    public function dashboard()
    {
        $suryaciptaStasion = 140323;
        $now = Carbon::now();
        $currentUnixEpochTime = $now->copy()->timestamp;
        $request = Http::get(env('WEATHERLINK_URL')."/current/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&api-signature={$this->currentWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime)}"); //for current
        $response = json_decode($request->getBody());

        $last_rain_rate = $response->sensors[0]->data[0]->rain_rate_mm;

        return view('welcome',compact('last_rain_rate'));
    }
}
