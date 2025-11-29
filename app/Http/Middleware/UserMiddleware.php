<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika user biasa → izinkan
        if (auth()->check() && auth()->user()->role === 'user') {
            return $next($request);
        }

        // Jika admin → kembalikan ke dashboard admin
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Admin tidak boleh mengakses halaman user.');
        }

        // Jika tidak login
        return redirect()->route('login');
    }
}