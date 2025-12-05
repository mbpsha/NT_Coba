<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

// Prefix 'api' sudah ada, tidak perlu ditambah lagi
Route::group([], function () {
    Route::get('/provinces', [LocationController::class, 'provinces'])->name('api.provinces');
    Route::get('/cities', [LocationController::class, 'cities'])->name('api.cities');
});

