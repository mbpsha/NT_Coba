<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'status' => fake()->randomElement(['pending', 'diproses', 'dikirim', 'selesai']),
            'total_harga' => fake()->numberBetween(50000, 500000),
            'id_alamat' => Address::factory(),
            'shipping_cost' => fake()->numberBetween(5000, 50000),
            'admin_fee' => 5000,
            'shipping_weight' => fake()->numberBetween(100, 5000),
            'shipping_destination_city_id' => fake()->numberBetween(1, 500),
            'shipping_courier' => fake()->randomElement(['JNE', 'POS', 'TIKI', 'JNT', 'SICEPAT', 'ANTERAJA']),
            'shipping_service' => fake()->randomElement(['REG', 'YES', 'OKE', 'EXPRESS']),
            'shipping_etd' => fake()->numberBetween(1, 7) . '-' . fake()->numberBetween(3, 10),
            'shipping_is_estimated' => fake()->randomElement([0, 1]),
        ];
    }
}
