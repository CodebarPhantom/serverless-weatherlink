<?php

namespace App\Console\Commands;

use App\Exports\WeatherHistoryExport;
use App\Models\WeatherHistoryReport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;

class DailyExportWeatherHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weatherlink-api:daily-export-weather-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export from DB to Excel Weather History';

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
        $now = carbon::now();
        //$now = Carbon::parse("2023-02-14 00:00:27"); // untuk generata hari sbelumnya harus h+1
        $hoursNow = $now->copy()->format("H");
        $minutesNow = $now->copy()->format("i");

        if((int)$hoursNow === 0 && (int)$minutesNow === 0){
            $reportDate = $now->copy()->subDay()->format('d-m-Y');
        }else{
            $reportDate = $now->copy()->format('d-m-Y');
        }



        $path = "weather-history/karawang/{$reportDate}_WeatherHistory-karawang.xlsx";
        Excel::store(new WeatherHistoryExport($now), $path, 's3_public', null, [
            'visibility' => 'public',
        ]);

        $checkReportNameExists = WeatherHistoryReport::whereName("Weather Report Karawang {$reportDate}")->first();

        !empty($checkReportNameExists) ?
            $weatherHistoryReport  =  $checkReportNameExists
            :$weatherHistoryReport = new WeatherHistoryReport();

        DB::beginTransaction();
        try {

            $weatherHistoryReport->name = "Weather Report Karawang {$reportDate}";
            $weatherHistoryReport->path_s3 = $path;
            $weatherHistoryReport->master_station_id = 140323;
            $weatherHistoryReport->save();

        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return abort(500);
        }
        DB::commit();
    }
}
