<?php

namespace Tests\Feature\Product;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-04 BONUS: Admin Add Multiple Products
 * Test admin bisa menambahkan beberapa product sekaligus dalam 1 session
 */
class Step3b_AddMultipleProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_add_multiple_products(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin (1x login untuk semua operasi)
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        echo "\n=== TC-INT-04 BONUS: Admin Add Multiple Products ===\n\n";

        // ============================================================
        // ADD PRODUCT #1
        // ============================================================
        echo "1️⃣ ADD PRODUCT #1\n";
        
        $product1Data = [
            'nama_produk' => 'Beras Organik Premium',
            'deskripsi' => 'Beras organik berkualitas tinggi',
            'harga' => 50000,
            'stok' => 100,
            'kategori' => 'beras',
        ];

        $addResponse1 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/products', $product1Data);

        $addResponse1->assertStatus(201)
            ->assertJson(['message' => 'Product created successfully!']);

        $productId1 = $addResponse1->json('product.id_produk');

        echo "✓ Product #1 berhasil ditambahkan\n";
        echo "  - ID: {$productId1}\n";
        echo "  - Nama: Beras Organik Premium\n";
        echo "  - Harga: Rp 50.000\n\n";

        // Verify product 1 ada di database
        $this->assertDatabaseHas('products', [
            'id_produk' => $productId1,
            'nama_produk' => 'Beras Organik Premium',
        ]);

        // ============================================================
        // ADD PRODUCT #2
        // ============================================================
        echo "2️⃣ ADD PRODUCT #2\n";
        
        $product2Data = [
            'nama_produk' => 'Jagung Manis Segar',
            'deskripsi' => 'Jagung manis langsung dari petani',
            'harga' => 30000,
            'stok' => 150,
            'kategori' => 'sayuran',
        ];

        $addResponse2 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/products', $product2Data);

        $addResponse2->assertStatus(201)
            ->assertJson(['message' => 'Product created successfully!']);

        $productId2 = $addResponse2->json('product.id_produk');

        echo "✓ Product #2 berhasil ditambahkan\n";
        echo "  - ID: {$productId2}\n";
        echo "  - Nama: Jagung Manis Segar\n";
        echo "  - Harga: Rp 30.000\n\n";

        // Verify product 2 ada di database
        $this->assertDatabaseHas('products', [
            'id_produk' => $productId2,
            'nama_produk' => 'Jagung Manis Segar',
        ]);

        // ============================================================
        // ADD PRODUCT #3
        // ============================================================
        echo "3️⃣ ADD PRODUCT #3\n";
        
        $product3Data = [
            'nama_produk' => 'Tomat Cherry Fresh',
            'deskripsi' => 'Tomat cherry segar dan manis',
            'harga' => 25000,
            'stok' => 200,
            'kategori' => 'sayuran',
        ];

        $addResponse3 = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/products', $product3Data);

        $addResponse3->assertStatus(201)
            ->assertJson(['message' => 'Product created successfully!']);

        $productId3 = $addResponse3->json('product.id_produk');

        echo "✓ Product #3 berhasil ditambahkan\n";
        echo "  - ID: {$productId3}\n";
        echo "  - Nama: Tomat Cherry Fresh\n";
        echo "  - Harga: Rp 25.000\n\n";

        // Verify product 3 ada di database
        $this->assertDatabaseHas('products', [
            'id_produk' => $productId3,
            'nama_produk' => 'Tomat Cherry Fresh',
        ]);

        // ============================================================
        // VERIFY ALL 3 PRODUCTS EXIST
        // ============================================================
        echo "✅ SUMMARY:\n";
        echo "Total products berhasil ditambahkan: 3\n";
        echo "  1. Beras Organik Premium (ID: {$productId1})\n";
        echo "  2. Jagung Manis Segar (ID: {$productId2})\n";
        echo "  3. Tomat Cherry Fresh (ID: {$productId3})\n";
        
        // Pastikan semua product ada di database
        $this->assertDatabaseCount('products', 3);

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
