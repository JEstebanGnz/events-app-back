<?php

use App\Http\Controllers\UserController;
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
Route::post('auth/credentials/validate', [\App\Http\Controllers\AuthController::class, 'handleCredentialsAuth']);
/* >>>>>>>>>>>>>>>>>>>>>>>  Auth routes >>>>>>>><<<<<< */

Route::get('users/{userId}/hasUnreadMessages', [\App\Http\Controllers\UserController::class, 'hasUnreadMessages']);

Route::get('users/{userId}/markReadMessages', [\App\Http\Controllers\UserController::class, 'markReadMessages']);

Route::put('users/{userId}/roles', [UserController::class, 'updateRoles']);

Route::resource('users', UserController::class,
    [  'as' => 'api']);

Route::resource('roles', \App\Http\Controllers\RoleController::class,
    [  'as' => 'api']);

Route::post('userInfo', [UserController::class, 'userInfo']);

Route::resource('events', \App\Http\Controllers\EventController::class,
    [  'as' => 'api']);

Route::resource('event/{id}/messages', \App\Http\Controllers\MessageController::class,
    [  'as' => 'api']);

Route::get('event/{id}/messages/set', [\App\Http\Controllers\EventFilesController::class, 'getLogo']);

Route::post('event/{id}/users/assign', [\App\Http\Controllers\EventController::class, 'assignUsers']);

Route::resource('event/{id}/files', \App\Http\Controllers\EventFilesController::class,
    [  'as' => 'api']);

Route::get('event/{id}/eventFiles/logo', [\App\Http\Controllers\EventFilesController::class, 'getLogo']);

Route::resource('event/{id}/meetings', \App\Http\Controllers\EventMeetingsController::class,
    [  'as' => 'api']);
