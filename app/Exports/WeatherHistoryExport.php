<?php

namespace App\Exports;

use App\Models\WeatherHistory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\WithMapping;
// use Illuminate\Support\Facades\Date;
use Carbon\Carbon;


class WeatherHistoryExport implements FromView //FromQuery, WithMapping
{

    public function view(): View
    {

        $start = Carbon::now()->startOfDay()->timestamp;
        $end = Carbon::now()->endOfDay()->timestamp;

        $datas = WeatherHistory::where('unix_epoch_time','>=',$start)
        ->where('unix_epoch_time','<=',$end)
        ->orderBy('unix_epoch_time','asc')->get();


        return view('exports.weather-history-template',compact('datas'));
    }

    // public function headings(): array
    // {
    //     return [
    //         //'No.',
    //         'Rain Rate (mm)',
    //         'Waktu',
    //     ];
    // }

    // public function map($weatherHistory): array
    // {
    //     return [
    //         $weatherHistory->rain_rate_hi_mm == 0 ? "0" : $weatherHistory->rain_rate_hi_mm,
    //         Carbon::createFromTimestamp($weatherHistory->unix_epoch_time)->format('d/m/Y H:i'),
    //     ];
    // }

    // public function query()
    // {
    //     $start = Carbon::now()->format("Y-m-d 00:00:00");
    //     $end = Carbon::now()->format("Y-m-d 23:59:59");

    //     return WeatherHistory::query()->where("created_at");
    // }
}
