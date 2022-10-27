<?php

namespace App\Exports;

use App\Models\WeatherHistory;
use Maatwebsite\Excel\Concerns\FromCollection;

class WeatherHistoryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return WeatherHistory::all();
    }
}
