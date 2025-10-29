<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthController;

// Root → redirect ke dashboard
Route::get('/', fn () => redirect()->route('dashboard'));

// ==== AUTH (guest) ====
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', fn () => Inertia::render('Auth/Register'))->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ==== DASHBOARD (public: guest & auth boleh) ====
Route::get('/dashboard', fn () => Inertia::render('User/Dashboard'))
    ->name('dashboard');

// ==== PROTECTED ====
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
