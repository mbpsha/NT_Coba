<?php

use Illuminate\Support\Facades\Route;

// Serve SPA entry for all routes handled by Vue Router, but exclude /api/* paths
Route::view('/{path?}', 'app')->where('path', '^(?!api).*$');
