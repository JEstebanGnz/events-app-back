<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
Route::post('auth/google', [\App\Http\Controllers\AuthController::class, 'handleGoogleAuth']);

Route::post('auth/credentials', [\App\Http\Controllers\AuthController::class, 'handleCredentialsAuth']);


Route::resource('events', \App\Http\Controllers\EventController::class,
    [  'as' => 'api']);

Route::resource('event/{id}/messages', \App\Http\Controllers\MessageController::class,
    [  'as' => 'api']);

Route::resource('event/{id}/files', \App\Http\Controllers\EventFilesController::class,
    [  'as' => 'api']);

Route::get('event/{id}/eventFiles/logo', [\App\Http\Controllers\EventFilesController::class, 'getLogo']);

Route::resource('event/{id}/meetings', \App\Http\Controllers\EventMeetingsController::class,
    [  'as' => 'api']);

Route::get('/addUser', function (Request $request) {

    $user = $request->input('user');

    DB::table('users')->insert(['name' => $user['name'], 'email' => $user['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($user['password'])]);


});

Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('userData/test', [\App\Http\Controllers\AuthController::class, 'userInfo']);
    Route::get('check-auth',[\App\Http\Controllers\AuthController::class, function () {
        return response()->json(['authenticated' => true]);
    }]);
});
