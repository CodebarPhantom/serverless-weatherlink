<?php

namespace App\Http\Controllers\Web\Estate\WeatherHistory;

use App\Models\WeatherHistoryReport;
use Yajra\DataTables\DataTables;
use App\Http\Traits\APISignatureWeatherlink;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\WeatherHistory;



class WeatherHistoryReportController extends Controller
{
    use APISignatureWeatherlink;

    public function report()
    {
        return view('estate.weather-history.report');
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

    public function index()
    {
        // $suryaciptaStasion = 140323;
        // $now = Carbon::now();
        // $currentUnixEpochTime = $now->copy()->timestamp;
        // $request = Http::get(env('WEATHERLINK_URL')."/current/{$suryaciptaStasion}?api-key=".env('WEATHERLINK_API_KEY')."&t={$currentUnixEpochTime}&api-signature={$this->currentWeatherHMAC($suryaciptaStasion,$currentUnixEpochTime)}"); //for current
        // $response = json_decode($request->getBody());
        //$last_rain_rate = $response->sensors[0]->data[0]->rain_rate_mm;

        $last_rain_rate = WeatherHistory::orderBy('created_at','desc')->first()->rain_rate_hi_mm;
        $last_30minutes_rain_rates = WeatherHistory::latest()->take(6)->get();

        $datas = $last_30minutes_rain_rates->reverse();
        //$_rain_rate_hi_mm_datas = $last_30minutes_rain_rates->pluck('rain_rate_hi_mm')->reverse();
        //$_rain_rate_datas = $last_30minutes_rain_rates->pluck('rain_rate')->reverse();

        foreach ($datas as $data) {
            $labels[] = Carbon::createFromTimestamp($data->unix_epoch_time)->format('H:i');
            $rain_rate_hi_mm_datas[] = $data->rain_rate_hi_mm;
            $rain_rate_datas[] = $data->rain_rate;

        }



        //dd($labels,$rain_rate_hi_mm_datas, $rain_rate_datas);

        return view('estate.weather-history.index',compact('last_rain_rate','labels','rain_rate_hi_mm_datas','rain_rate_datas'));
    }
}
