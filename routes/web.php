<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Estate\WeatherHistory\WeatherHistoryReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome',[
//         'last_rain_rate' => WeatherHistory::orderBy('created_at','desc')->first()->rain_rate_hi_mm
//     ]);
// });

// Route::get('/',[WeatherHistoryReportController::class,'index']);

Route::redirect('/', '/suryacipta', 301);
Route::get('/suryacipta', [WeatherHistoryReportController::class, 'indexSuryacipta'])->name('index-suryacipta');
Route::get('/smartpolitan', [WeatherHistoryReportController::class, 'indexSmartpolitan'])->name('index-smartpolitan');

Route::as('weather-history.')->group(function(){
    Route::get('/report',[WeatherHistoryReportController::class,'report'])->name('report');
    Route::post('/report/data',[WeatherHistoryReportController::class,'data'])->name('report-data');
});
