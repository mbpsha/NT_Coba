<?php

namespace Tests\Feature\Product;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-04 STEP 3: Admin Add Product
 * Test admin bisa menambahkan product baru
 */
class Step3_AddProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_add_product(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        echo "\n=== TC-INT-04 STEP 3: Admin Add Product ===\n";

        // Data product baru
        $productData = [
            'nama_produk' => 'Beras Organik Premium',
            'deskripsi' => 'Beras organik berkualitas tinggi',
            'harga' => 50000,
            'stok' => 100,
            'kategori' => 'beras',
        ];

        // Add product
        $addResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/products', $productData);

        // Cek product berhasil ditambahkan
        $addResponse->assertStatus(201)
            ->assertJson(['message' => 'Product created successfully!'])
            ->assertJsonStructure(['product' => ['id_produk', 'nama_produk']]);

        $newProductId = $addResponse->json('product.id_produk');

        echo "✓ Admin berhasil tambah product\n";
        echo "  - ID: {$newProductId}\n";
        echo "  - Nama: Beras Organik Premium\n";
        echo "  - Harga: Rp 50.000\n";
        echo "  - Stok: 100\n";

        // 💡 Uncomment untuk liat response JSON:
        // $addResponse->dump();

        // Verify product ada di database
        $this->assertDatabaseHas('products', [
            'id_produk' => $newProductId,
            'nama_produk' => 'Beras Organik Premium',
            'harga' => 50000,
        ]);

        echo "✓ Product berhasil tersimpan di database\n";

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
