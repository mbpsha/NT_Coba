<?php

namespace Tests\Feature\Product;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-04 STEP 2: Admin Akses Product Management Page
 * Test admin bisa akses halaman product management
 */
class Step2_AccessProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_access_product_page(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        echo "\n=== TC-INT-04 STEP 2: Admin Akses Product Page ===\n";

        // Akses halaman product management
        $productsResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/admin/products');

        // Cek bisa akses halaman
        $productsResponse->assertStatus(200)
            ->assertJsonStructure(['props' => ['products']]);

        echo "✓ Admin berhasil akses halaman product management\n";
        echo "✓ Response memiliki struktur yang benar (props.products)\n";

        // 💡 Uncomment untuk liat response JSON:
        // $productsResponse->dump();

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
