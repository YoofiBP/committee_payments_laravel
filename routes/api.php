<?php

use App\Http\Controllers\API\ContributionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EventController;

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

Route::apiResource('users', UserController::class);
Route::apiResource('events', EventController::class);

Route::prefix('contributions')->group(function () {
    Route::get('/verify', [ContributionController::class, 'verify']);
    Route::post('/contribute/{event}', [ContributionController::class,'initiatePaymentProvider']);
}
);
