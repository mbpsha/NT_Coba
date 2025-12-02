<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-03 STEP 2: Admin Akses Payment Verification Page
 * Test admin bisa akses halaman orders/payment
 */
class Step2_AccessPaymentPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_access_payment_page(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        echo "\n=== TC-INT-03 STEP 2: Admin Akses Payment Page ===\n";

        // Akses halaman orders (payment verification page)
        $ordersResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/admin/orders');

        // Cek bisa akses halaman
        $ordersResponse->assertStatus(200)
            ->assertJsonStructure(['props' => ['orders']]);

        echo "✓ Admin berhasil akses halaman orders\n";
        echo "✓ Response memiliki struktur yang benar (props.orders)\n";

        // 💡 Uncomment untuk liat response JSON:
        // $ordersResponse->dump();

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
