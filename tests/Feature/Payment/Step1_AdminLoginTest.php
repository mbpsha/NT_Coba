<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-03 STEP 1: Admin Login
 * Test admin berhasil login dan dapet token
 */
class Step1_AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        echo "\n=== TC-INT-03 STEP 1: Admin Login ===\n";

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        // Cek login berhasil dan dapet token
        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);

        $token = $loginResponse->json('token');

        echo "✓ Admin berhasil login (email: admin@admin.com)\n";
        echo "✓ Token berhasil digenerate\n";

        // Verify token ada dan ga kosong
        $this->assertNotEmpty($token);

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
