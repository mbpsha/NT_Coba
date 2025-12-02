<?php

namespace Tests\Feature\Orders;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-05 STEP 2: Admin Akses Order Management Page
 * Test admin bisa akses halaman order management
 */
class Step2_AccessOrderPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_access_order_page(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        echo "\n=== TC-INT-05 STEP 2: Admin Akses Order Page ===\n";

        // Akses halaman order management
        $ordersResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/admin/orders');

        // Cek bisa akses halaman
        $ordersResponse->assertStatus(200)
            ->assertJsonStructure(['props' => ['orders']]);

        echo "✓ Admin berhasil akses halaman order management\n";
        echo "✓ Response memiliki struktur yang benar (props.orders)\n";

        // 💡 Uncomment untuk liat response JSON:
        // $ordersResponse->dump();

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
