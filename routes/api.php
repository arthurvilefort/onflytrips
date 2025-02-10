<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'userProfile']);
    Route::post('/trips', [TripController::class, 'store']);
    Route::put('/trips/{id}/status', [TripController::class, 'updateStatus']);
    Route::get('/trips/{id}', [TripController::class, 'show']);
    Route::get('/trips', [TripController::class, 'index']);
    Route::delete('/trips/{id}', [TripController::class, 'cancel']);
});