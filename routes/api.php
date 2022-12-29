<?php

use App\Http\Controllers\API\V1\WeatherlinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('api-oauth-client')->group(function() {
    Route::get('current-weather',[WeatherlinkController::class,"historic"]);
});
