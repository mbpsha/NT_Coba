<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_order' => Order::factory(),
            'metode_pembayaran' => fake()->randomElement(['qris', 'transfer_bank', 'cod', 'ewallet', 'kartu_kredit']),
            'jumlah' => fake()->randomFloat(2, 10000, 1000000),
            'status' => fake()->randomElement(['pending', 'verified', 'rejected']),
            'bukti_transfer' => fake()->randomElement([null, '/storage/payment_proofs/sample.png']),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the payment is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'verified',
            'bukti_transfer' => '/storage/payment_proofs/verified_' . fake()->uuid() . '.png',
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'bukti_transfer' => '/storage/payment_proofs/pending_' . fake()->uuid() . '.png',
        ]);
    }
}
