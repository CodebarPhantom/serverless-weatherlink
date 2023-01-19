<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Estate\WeatherlinkController;
use App\Http\Controllers\API\V1\MsGraph\Mail\MailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix("/v1")->as("v1.")->group(function(){

    Route::prefix("/msgraph")->as("ms-graph.")->group(function(){
        Route::prefix("/mail")->as("mail.")->group(function(){
            Route::post('/send',[MailController::class,'send'])->name('send');
        });
    });



    Route::prefix("/estate")->as("estate.")->group(function(){
        Route::get('/weather-historic',[WeatherlinkController::class,'historic'])->name('weather-historic');
    });
});
