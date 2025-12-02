<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_produk' => fake()->words(3, true),
            'deskripsi' => fake()->paragraph(),
            'harga' => fake()->randomFloat(2, 10000, 1000000),
            'gambar' => fake()->imageUrl(640, 480, 'products', true),
            'stok' => fake()->numberBetween(0, 100),
        ];
    }
}
