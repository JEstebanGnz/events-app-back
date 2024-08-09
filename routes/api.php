<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Revolution\Google\Sheets\Facades\Sheets;

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

/* >>>>>>>>>>>>>>>>>>>>>>>  Auth routes >>>>>>>><<<<<< */
Route::get('auth/google/redirect', [\App\Http\Controllers\AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [\App\Http\Controllers\AuthController::class, 'handleGoogleCallback']);
Route::post('auth/otp/validate', [\App\Http\Controllers\AuthController::class, 'authenticateUser']);

Route::resource('events', \App\Http\Controllers\EventController::class,
    [  'as' => 'api']);

Route::resource('event/{id}/messages', \App\Http\Controllers\MessageController::class,
    [  'as' => 'api']);

Route::resource('event/{id}/files', \App\Http\Controllers\EventFilesController::class,
    [  'as' => 'api']);

Route::get('event/{id}/eventFiles/logo', [\App\Http\Controllers\EventFilesController::class, 'getLogo']);

Route::get('/test', function () {
    $sheet = Sheets::spreadsheet(env('POST_SPREADSHEET_ID'))->sheet('Agenda')->get();
    $header = $sheet->pull(0);
    /*    dd($sheet,$header);*/
    $values = Sheets::collection($header, $sheet);
    $eventMeetings = array_values($values->toArray());
    dd($eventMeetings);
});

Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('userData', [\App\Http\Controllers\AuthController::class, 'userInfo']);
    Route::get('user',[\App\Http\Controllers\AuthController::class, 'userInfo']);
    Route::get('logout',[\App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('check-auth',[\App\Http\Controllers\AuthController::class, function () {
        return response()->json(['authenticated' => true]);
    }]);
});