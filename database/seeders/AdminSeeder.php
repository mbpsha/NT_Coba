<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat atau update admin berdasarkan email unik
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'no_telp' => '081234567890',
                'alamat' => 'Kantor Pusat',
                'role' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin123!'), // ganti setelah seeding
                'remember_token' => Str::random(10),
            ]
        );
    }
}
