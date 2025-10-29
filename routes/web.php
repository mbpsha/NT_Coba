<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Models\User;

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

// Admin Routes (Protected with auth + admin middleware)
Route::middleware(['auth', App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products Management
    Route::get('/products', [ProductController::class, 'indexAdmin'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Orders Management
    Route::get('/orders', [OrderController::class, 'indexAdmin'])->name('orders.index');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Payment Verification
    Route::get('/payments', [PaymentController::class, 'indexAdmin'])->name('payments.index');
    Route::put('/payments/{id}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
});
