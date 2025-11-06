<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
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
            'role' => 'nullable|in:admin,user'
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

        // Redirect based on user role after registration
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('message', 'Registration successful!');
        }

        return redirect()->route('dashboard')->with('message', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard()
    {
        return Inertia::render('User/Dashboard'); // pastikan file halamannya ada
    }

    // opsional kalau punya halaman lain
    public function toko()
    {
        return Inertia::render('User/Toko'); // jika pakai ShopController, abaikan method ini
    }
}
