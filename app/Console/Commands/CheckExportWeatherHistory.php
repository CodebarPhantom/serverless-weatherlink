<?php

namespace App\Console\Commands;

use App\Exports\WeatherHistoryExport;
use App\Models\WeatherHistoryReport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;
use App\Models\MasterStation;

class CheckExportWeatherHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weatherlink-api:check-export-weather-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export from DB to Excel Weather History Yesterday Check ';

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
        $now = carbon::now()->subDay()->endOfDay();
        //$now = Carbon::parse("2023-01-09")->endOfDay();
        //$hoursNow = $now->copy()->format("H");
        //$minutesNow = $now->copy()->format("i");
        $reportDate = $now->copy()->format('d-m-Y');

        $masterStations = MasterStation::get();


        foreach ($masterStations as $masterStation) {

            $path = "weather-history/{$masterStation->name}/{$reportDate}_WeatherHistory-{$masterStation->name}.xlsx";
            Excel::store(new WeatherHistoryExport($now,$masterStation->id), $path, 's3_public', null, [
                'visibility' => 'public',
            ]);

            $checkReportNameExists = WeatherHistoryReport::whereName("Weather Report {$masterStation->name} {$reportDate}")->first();

            !empty($checkReportNameExists) ?
                $weatherHistoryReport  =  $checkReportNameExists
                : $weatherHistoryReport = new WeatherHistoryReport();

            DB::beginTransaction();
            try {

                $weatherHistoryReport->name = "Weather Report {$masterStation->name} {$reportDate}";
                $weatherHistoryReport->path_s3 = $path;
                $weatherHistoryReport->master_station_id = $masterStation->id;
                $weatherHistoryReport->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                report($e);
                return abort(500);
            }
        }
    }
}
