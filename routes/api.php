<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::apiResource('blogs', BlogController::class);

// Routes untuk produk yang dapat diakses publik
Route::get('products', [ProductController::class, 'index']);           // Daftar semua produk
Route::get('products/{id}', [ProductController::class, 'show']);       // Detail produk
Route::get('products/popular/list', [ProductController::class, 'popular']); // Produk terpopuler

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);

    // Reviews
    Route::apiResource('reviews', ReviewController::class)->only(['index','store','update','destroy','show']);

    // Cart Management
    Route::get('cart', [CartController::class, 'show']);              // lihat keranjang
    Route::post('cart/items', [CartController::class, 'add']);        // tambah item
    Route::patch('cart/items/{id}', [CartController::class, 'updateQty']);
    Route::delete('cart/items/{id}', [CartController::class, 'remove']);
    Route::delete('cart/clear', [CartController::class, 'clear']);

    // Order Management
    Route::post('checkout', [OrderController::class, 'checkout']); // dari cart → order
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']); // admin/staff only

    // Payment Management
    Route::post('payments', [PaymentController::class, 'store']);          // create payment intent / upload bukti
    Route::get('orders/{order}/payment', [PaymentController::class, 'show']);

    // Product Management (Admin Only - tambahkan middleware admin nanti)
    Route::post('products', [ProductController::class, 'store']);          // Tambah produk baru
    Route::put('products/{id}', [ProductController::class, 'update']);     // Update produk
    Route::patch('products/{id}', [ProductController::class, 'update']);   // Update produk (PATCH)
    Route::delete('products/{id}', [ProductController::class, 'destroy']); // Hapus produk

    // Product Stock Management (Admin Only)
    Route::patch('products/{id}/stock', [ProductController::class, 'updateStock']); // Update stok
    Route::get('products/low-stock/list', [ProductController::class, 'lowStock']); // Produk stok menipis
    Route::patch('products/bulk-update', [ProductController::class, 'bulkUpdate']); // Bulk update produk
});
