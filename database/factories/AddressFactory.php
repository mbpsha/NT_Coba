<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
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
            'label' => fake()->randomElement(['Rumah', 'Kantor', 'Kos']),
            'nama_penerima' => fake()->name(),
            'no_telp_penerima' => '08' . fake()->numerify('##########'),
            'alamat_lengkap' => fake()->address(),
            'nama_jalan' => fake()->streetName(),
            'no_rumah' => fake()->buildingNumber(),
            'kelurahan_desa' => fake()->city(),
            'kecamatan' => fake()->city(),
            'kabupaten' => fake()->randomElement(['Surakarta', 'Yogyakarta', 'Semarang', 'Jakarta', 'Bandung', 'Surabaya']),
            'provinsi' => fake()->randomElement(['Jawa Tengah', 'DI Yogyakarta', 'DKI Jakarta', 'Jawa Barat', 'Jawa Timur']),
            'kode_pos' => fake()->numerify('#####'),
            'is_default' => false,
        ];
    }
}
