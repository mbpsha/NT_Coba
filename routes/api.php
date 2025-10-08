<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Test route
Route::post('/test', function() {
    return response()->json(['message' => 'Test OK']);
});

// Public routes - Authentication
Route::post('/register', function(Illuminate\Http\Request $request) {
    $request->validate([
        'nama' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'no_telp' => 'nullable|string|max:20',
        'alamat' => 'nullable|string',
        'role' => 'nullable|in:admin,user'
    ]);

    $user = App\Models\User::create([
        'nama' => $request->nama,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Illuminate\Support\Facades\Hash::make($request->password),
        'no_telp' => $request->no_telp,
        'alamat' => $request->alamat,
        'role' => $request->role ?? 'user'
    ]);

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
        'message' => 'Registration successful',
        'user' => $user,
        'token' => $token
    ], 201);
});
Route::post('/login', function(Illuminate\Http\Request $request) {
    $request->validate([
        'login' => 'required|string', // Bisa email atau username
        'password' => 'required'
    ]);

    $loginField = $request->login;

    // Cek apakah input adalah email atau username
    $user = null;
    if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
        // Jika format email valid, cari berdasarkan email
        $user = App\Models\User::where('email', $loginField)->first();
    } else {
        // Jika bukan email, cari berdasarkan username
        $user = App\Models\User::where('username', $loginField)->first();
    }

    if (!$user || !Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token
    ]);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Protected auth routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\ApiAuthController::class, 'logout']);
    Route::get('/auth/user', [App\Http\Controllers\Auth\ApiAuthController::class, 'user']);
});

// Public routes (tidak perlu authentication)
Route::apiResource('products', App\Http\Controllers\ProductController::class)->only(['index', 'show']);
Route::apiResource('blogs', App\Http\Controllers\BlogController::class)->only(['index', 'show']);

// Protected routes (perlu authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // User routes
    Route::apiResource('reviews', App\Http\Controllers\ReviewController::class);
    Route::apiResource('carts', App\Http\Controllers\CartController::class);
    Route::apiResource('addresses', App\Http\Controllers\AddressController::class);

    // Cart detail routes
    Route::apiResource('cart-details', App\Http\Controllers\CartDetailController::class);
    Route::get('carts/{cartId}/items', [App\Http\Controllers\CartDetailController::class, 'getCartItems']);
    Route::patch('cart-details/{id}/quantity', [App\Http\Controllers\CartDetailController::class, 'updateQuantity']);

    // User addresses
    Route::get('users/{userId}/addresses', [App\Http\Controllers\AddressController::class, 'getUserAddresses']);

    // Checkout routes
    Route::post('checkout', [App\Http\Controllers\CheckoutController::class, 'processCheckout']);
    Route::get('orders/{orderId}/tracking', [App\Http\Controllers\CheckoutController::class, 'getOrderTracking']);

    // User orders
    Route::apiResource('orders', App\Http\Controllers\OrderController::class)->only(['index', 'show']);
});

// Admin routes (perlu authentication + admin role)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin CRUD
    Route::apiResource('products', App\Http\Controllers\ProductController::class)->except(['index', 'show']);
    Route::apiResource('blogs', App\Http\Controllers\BlogController::class)->except(['index', 'show']);
    Route::apiResource('orders', App\Http\Controllers\OrderController::class)->except(['index', 'show']);
    Route::apiResource('payments', App\Http\Controllers\PaymentController::class);

    // Dashboard routes
    Route::get('dashboard/statistics', [App\Http\Controllers\DashboardController::class, 'getStatistics']);
    Route::get('dashboard/today-transactions', [App\Http\Controllers\DashboardController::class, 'getTodayTransactions']);
    Route::get('dashboard/sales-data', [App\Http\Controllers\DashboardController::class, 'getSalesData']);
    Route::get('dashboard/product-stats', [App\Http\Controllers\DashboardController::class, 'getProductStats']);

    // Admin actions
    Route::patch('payments/{paymentId}/verify', [App\Http\Controllers\DashboardController::class, 'verifyPayment']);
    Route::patch('orders/{orderId}/status', [App\Http\Controllers\DashboardController::class, 'updateOrderStatus']);
});
