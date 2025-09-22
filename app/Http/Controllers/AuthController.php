<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Register user baru
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            // butuh field password_confirmation juga
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        // Buat token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'    => 'Register berhasil',
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    // Login pakai email atau username
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string', // bisa email / username
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login gagal, periksa email/username dan password'], 401);
        }

        // Hapus token lama biar 1 user 1 session (opsional)
        $user->tokens()->delete();

        // Bikin token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'    => 'Login berhasil',
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer'
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    // Profil user
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }
}
