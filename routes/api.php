<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('api')->group(function () {
    Route::prefix('v1')->controller(WeatherController::class)->group(function () {
        // Route param pattern you may see on App\Providers\RouteServiceProvider
        Route::get('weather', 'getWeather');
        Route::get('forecast', 'getForecast');
        Route::get('city', 'getCity');  
    });
});