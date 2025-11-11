<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\News;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NewsController;

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
Route::get('/dashboard', function () {
    $latestNews = News::published()->latest()->take(5)->get(); // kirim untuk carousel
    return Inertia::render('User/Dashboard', [
        'welcome'     => 'Halo',
        'latestNews'  => $latestNews,
    ]);
})->name('dashboard');

// Produk - detail publik
Route::get('/produk/{id_produk}', [ProductController::class, 'show'])->name('produk.show');

// Berita Dinamis
Route::get('/berita',    [NewsController::class, 'index'])->name('berita');
Route::get('/berita/{id}', [NewsController::class, 'show'])->name('berita.show');

// arahkan langsung ke komponen Inertia (tanpa method controller)
Route::get('/blog',   fn () => Inertia::render('User/Blog'))->name('blog');
Route::get('/about',  fn () => Inertia::render('User/About'))->name('about');

// Toko
Route::get('/toko', [TokoController::class, 'index'])->name('toko');
// alias lama, aman jika masih ada pemanggilan 'shop'
Route::get('/shop', fn () => redirect()->route('toko'))->name('shop');

// Checkout (butuh login)
Route::middleware('auth')->group(function () {
    Route::get('/checkout/{id_produk}', [CheckoutController::class, 'show'])->name('checkout');
});

// Protected
Route::middleware(['auth'])->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/detail/{id_detail_keranjang}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/detail/{id_detail_keranjang}', [CartController::class, 'remove'])->name('cart.remove');
});

// Admin (protected) — hanya auth + AdminMiddleware
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
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