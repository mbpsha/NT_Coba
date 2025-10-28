<?php

use Illuminate\Support\Facades\Route;

// Serve SPA entry for all routes handled by Vue Router
Route::view('/{any}', 'app')->where('any', '.*');
