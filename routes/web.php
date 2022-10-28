<?php

use App\Models\WeatherHistory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherHistoryReportController;

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

Route::get('/', function () {
    return view('welcome',[
        'last_rain_rate' => WeatherHistory::orderBy('created_at','desc')->first()
    ]);
});

Route::as('weather-history.')->group(function(){
    Route::get('/report',[WeatherHistoryReportController::class,'index'])->name('report');
    Route::post('/report/data',[WeatherHistoryReportController::class,'data'])->name('report-data');
});
