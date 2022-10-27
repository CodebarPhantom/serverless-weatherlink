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
        $now = carbon::now()->subDay()->format('d-m-Y');
        $path = "weather-history/{$now}_WeatherHistory.xlsx";
        Excel::store(new WeatherHistoryExport, $path, 's3_public', null, [
            'visibility' => 'public',
        ]);

        DB::beginTransaction();
        try {

            $weatherHistoryReport = new WeatherHistoryReport();
            $weatherHistoryReport->name = "Report Cuaca {$now}";
            $weatherHistoryReport->path_s3 = $path;
            $weatherHistoryReport->save();

        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return abort(500);
        }
        DB::commit();
    }
}
