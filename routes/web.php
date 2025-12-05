<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\News;
use App\Models\User;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;
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
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Root -> dashboard publik
Route::get('/', fn () => redirect()->route('dashboard'));

// Auth (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

// Halaman publik
Route::get('/dashboard', function () {
    if (auth()->check()) {
        // Jika admin -> redirect ke admin dashboard
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
    }
    // Jika bukan admin (termasuk guest), tampilkan dashboard user seperti biasa
    $latestNews = News::published()->latest()->take(5)->get();

    return Inertia::render('User/Dashboard', [
        'welcome'     => 'Halo',
        'latestNews'  => $latestNews,
    ]);
})->name('dashboard');

// Berita Dinamis
    Route::get('/berita',    [NewsController::class, 'index'])->name('berita');
    Route::get('/berita/{id}', [NewsController::class, 'show'])->name('berita.show');
    Route::get('/blog',   fn () => Inertia::render('User/Blog'))->name('blog');
    Route::get('/about',  fn () => Inertia::render('User/About'))->name('about');


Route::middleware(['auth', 'user'])->group(function () {
    // Produk - detail publik
    Route::get('/produk/{id_produk}', [ProductController::class, 'show'])->name('produk.show');

    // Toko
    Route::get('/toko', [TokoController::class, 'index'])->name('toko');
    Route::get('/shop', fn () => redirect()->route('toko'))->name('shop');
});

// Protected - No verification required (browsing, cart, etc)
Route::middleware('auth')->group(function () {
    // Cart (bebas akses tanpa verifikasi)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.update.qty');
    Route::put('/cart/detail/{id_detail_keranjang}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/detail/{id_detail_keranjang}', [CartController::class, 'remove'])->name('cart.remove');
});

// Protected - Require email verification (profil & checkout/order)
Route::middleware(['auth', 'verified', 'user'])->group(function () {
    // Profil - WAJIB VERIFIKASI EMAIL
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Checkout & Order - WAJIB VERIFIKASI EMAIL
    Route::get('/checkout/cart', [CheckoutController::class, 'showCartCheckout'])->name('checkout.cart');
    Route::get('/checkout/cart/address', [CheckoutController::class, 'showCartAddressForm'])->name('checkout.cart.address');
    Route::post('/checkout/cart/address', [CheckoutController::class, 'saveCartAddress'])->name('checkout.cart.address.save');
    Route::get('/checkout/{id_produk}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::get('/checkout/{id_produk}/address', [CheckoutController::class, 'showAddressForm'])->name('checkout.address');
    Route::post('/checkout/{id_produk}/address', [CheckoutController::class, 'saveAddress'])->name('checkout.address.save');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');

    // Order & Payment - WAJIB VERIFIKASI EMAIL
    Route::post('/order/{id_produk}/create', [OrderController::class, 'createFromCheckout'])->name('order.create');
    Route::post('/order/cart/create', [OrderController::class, 'createFromCart'])->name('order.cart.create');
    Route::post('/payment/{id_order}/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');

    // User Orders (Pesanan Saya) - WAJIB VERIFIKASI EMAIL
    Route::get('/pesanan-saya', [OrderController::class, 'myOrders'])->name('orders.my');

    // Reviews
    Route::get('/review', [ReviewController::class, 'ratingPage'])->name('reviews.index');
    Route::post('/review', [ReviewController::class, 'submitFromUser'])->name('reviews.store');
});

// Logout (tidak perlu email verification)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
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

    // News Management
    Route::get('/news', [NewsController::class, 'indexAdmin'])->name('news.index');
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::get('/news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'indexAdmin'])->name('orders.index');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Payments
    Route::get('/payments', [PaymentController::class, 'indexAdmin'])->name('payments.index');
    Route::put('/payments/{id}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
});
