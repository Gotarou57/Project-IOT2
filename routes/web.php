<?php

use App\Http\Controllers\SensorController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Legacy redirect
Route::get('/', [SensorController::class, 'index']);
Route::get('/dashboard', [SensorController::class, 'index']);

// Main pages
Route::get('/home', [SensorController::class, 'home'])->name('home');
Route::get('/suhu', [SensorController::class, 'suhu'])->name('suhu');
Route::get('/humidity', [SensorController::class, 'humidity'])->name('humidity');

// Settings
Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
