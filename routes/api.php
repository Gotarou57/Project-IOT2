<?php

use App\Http\Controllers\Api\SensorDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('sensors')->group(function () {
    Route::get('/overview',    [SensorDataController::class, 'overview']);
    Route::get('/temperature', [SensorDataController::class, 'temperature']);
    Route::get('/humidity',    [SensorDataController::class, 'humidity']);
    Route::get('/air-quality', [SensorDataController::class, 'airQuality']);
});
