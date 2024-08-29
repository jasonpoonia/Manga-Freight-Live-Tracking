<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StarshipitController;
use App\Http\Controllers\MyFastwayController;


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

Route::middleware('cors')->group(function () {
    Route::get('/tasks', [App\Http\Controllers\TrackApiController::class, 'tasks']);
    Route::get('/task-events/{taskId}', [App\Http\Controllers\TrackApiController::class, 'taskEvents']);
    Route::get('/task-events', [App\Http\Controllers\TrackApiController::class, 'taskEventsByOrder']);
    Route::get('/tracking-credentials', [StarshipitController::class, 'getTrackingCredentials']);
    Route::get('/myfastway-tracking', [MyFastwayController::class, 'track']);
});
