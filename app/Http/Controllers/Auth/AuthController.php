<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\News;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register', [
            'recaptchaSiteKey' => config('captcha.sitekey')
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required'
        ]);

        $loginField = $request->login;

        // Check if input is email or username
        $user = User::where('email', $loginField)
                   ->orWhere('username', $loginField)
                   ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::login($user);

        // Check if email is verified (skip for admin)
        if ($user->role !== 'admin' && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('message', 'Silakan verifikasi email Anda untuk dapat mengakses fitur Profil dan Pemesanan. Cek inbox email Anda.');
        }

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('message', 'Login successful!');
        }

        return redirect()->route('dashboard')->with('message', 'Login successful!');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'role' => 'nullable|in:admin,user',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'role' => $request->role ?? 'user'
        ]);

        Auth::login($user);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Redirect to verification notice page
        return redirect()->route('verification.notice')->with('message', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
    }

    public function logout(Request $request)
    {
        $isAdmin = $request->user() && $request->user()->role === 'admin';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dashboard');
    }

    public function blog()
    {
        return Inertia::render('User/Blog');
    }

    public function about()
    {
        return Inertia::render('User/About');
    }

    public function dashboard()
    {
        $latestNews = News::published()->latest()->take(5)->get();
        return Inertia::render('User/Dashboard', [
            'latestNews' => $latestNews
        ]);
    }

    public function berita()
    {
        $latestNews = News::published()->latest()->take(5)->get();
        return Inertia::render('User/Berita', [
            'latestNews' => $latestNews
        ]);
    }
}
