<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user for testing
        User::factory()->admin()->create();

        // optional: panggil seeder lain dulu (users/products) jika ada
        $this->call([
            OrderPaymentSeeder::class,
        ]);
    }
}
