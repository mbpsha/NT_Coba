<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\TokoController;

// Root -> dashboard publik
Route::get('/', fn () => redirect()->route('dashboard'));

// Auth (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Halaman publik
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

// arahkan langsung ke komponen Inertia (tanpa method controller)
Route::get('/berita', fn () => Inertia::render('User/Berita'))->name('berita');
Route::get('/blog',   fn () => Inertia::render('User/Blog'))->name('blog');
Route::get('/about',  fn () => Inertia::render('User/About'))->name('about');

// opsional: /toko redirect ke /shop (TokoController)
Route::get('/toko', fn () => redirect()->route('shop'))->name('toko');

// Protected
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/shop', [TokoController::class, 'index'])->name('shop');
});

// Admin (protected) — hanya auth + AdminMiddleware
Route::middleware([
    App\Http\Middleware\AdminMiddleware::class,
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::get('/products', [ProductController::class, 'indexAdmin'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'indexAdmin'])->name('orders.index');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Payments
    Route::get('/payments', [PaymentController::class, 'indexAdmin'])->name('payments.index');
    Route::put('/payments/{id}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
});
