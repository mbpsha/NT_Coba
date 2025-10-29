<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // optional: panggil seeder lain dulu (users/products) jika ada
        $this->call([
            OrderPaymentSeeder::class,
        ]);
    }
}