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
    public $date;

    function __construct($date) {
        $this->date = $date;
    }

    public function view(): View
    {
        //$now = Carbon::now();
        $now = $this->date; //ambil dari construct
        $hoursNow = $now->copy()->format("H");
        $minutesNow = $now->copy()->format("i");


        if((int)$hoursNow === 0 && (int)$minutesNow === 0){
            $start = $now->copy()->subDay()->startOfDay()->timestamp;
            $end =  $now->copy()->subDay()->endOfDay()->timestamp;
        }else{
            $start =  $now->copy()->startOfDay()->timestamp;
            $end =  $now->copy()->endOfDay()->timestamp;
        }

        //dd($now, $hoursNow, $minutesNow,$start,$end);

        $datas = WeatherHistory::where('master_station_id', 140323) //karawang dlu aja deh excelnya
        ->where('unix_epoch_time','>=',$start)
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
