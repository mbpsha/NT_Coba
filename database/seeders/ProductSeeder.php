<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'IoT Stech Smart Garden (tanpa panel surya)',
            'variant' => 'Tanpa Panel Surya',
            'description' => 'Sistem irigasi otomatis berbasis IoT ...',
            'price' => 1500000,
        ]);

        Product::create([
            'name' => 'IoT Stech Smart Garden (dengan panel surya)',
            'variant' => 'Dengan Panel Surya',
            'description' => 'Varian ramah lingkungan dengan panel surya ...',
            'price' => 2500000,
        ]);
    }
}
