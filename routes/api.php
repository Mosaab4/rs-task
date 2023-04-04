<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\TripController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\StoreReservationController;
use App\Http\Controllers\Api\V1\CheckAvailableSeatsController;

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', LoginController::class)->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('trips', [TripController::class, 'index'])->name('trip.index');
        Route::get('trips/{trip}', [TripController::class, 'show'])->name('trip.show');
        Route::post('check-available-seats', CheckAvailableSeatsController::class)->name('check-available-seats');
        Route::post('reservations', StoreReservationController::class)->name('reservations.store');
    });
});

