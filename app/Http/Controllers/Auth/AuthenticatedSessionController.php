<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     * NOTE: This controller is not used in API-only authentication.
     * Use routes/api.php login endpoint instead.
     */
    public function store(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        // This controller should not be used with API-only auth
        return response()->json([
            'error' => 'This endpoint is deprecated. Use /api/login instead.'
        ], 410);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
