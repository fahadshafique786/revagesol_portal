<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Applications;
use App\Http\Controllers\API\SportsApi;
use App\Http\Controllers\API\LeaguesApi;
use App\Http\Controllers\API\TeamsApi;
use App\Http\Controllers\API\SchedulesApi;
use App\Http\Controllers\API\ServersApi;


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


//Route::group(['middleware' => 'apilogger'], function() {
    Route::post('app-details', [Applications::class, 'index']);
    Route::get('sports', [SportsApi::class, 'index']);
    Route::post('leagues', [LeaguesApi::class, 'index']);
    Route::post('teams', [TeamsApi::class, 'index']);
    Route::post('schedules', [SchedulesApi::class, 'index']);
    Route::post('servers', [ServersApi::class, 'index']);
    Route::post('stream-token', [Applications::class, 'getStreamToken']);
//});




// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


