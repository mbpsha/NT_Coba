<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;

Route::prefix('api')->group(function () {
    // Endpoint untuk mendapatkan daftar provinsi
    Route::get('/provinces', [LocationController::class, 'provinces'])->name('api.provinces');

    // Endpoint untuk mendapatkan daftar kota berdasarkan provinsi
    Route::get('/cities', [LocationController::class, 'cities'])->name('api.cities');
});

