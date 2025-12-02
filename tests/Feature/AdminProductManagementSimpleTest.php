<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-04: Admin Product Management - Simplified Test
 *
 * Test Flow:
 * 1. Admin login
 * 2. Admin akses halaman management produk
 * 3. Admin add product
 * 4. Admin edit product (ID produk yang baru ditambahkan)
 * 5. Admin hapus product
 */
class AdminProductManagementSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Test: Full flow TC-INT-04
     * 1. Admin login
     * 2. Akses product management page
     * 3. Add product
     * 4. Edit product
     * 5. Delete product
     */
    public function test_admin_product_management_flow(): void
    {
        // === STEP 1: Admin Login ===
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);

        $this->token = $loginResponse->json('token');
        echo "\n✓ Step 1: Admin berhasil login (email: admin@admin.com)";

        // === STEP 2: Admin akses product management page ===
        $productsResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/admin/products');

        $productsResponse->assertStatus(200)
            ->assertJsonStructure(['props' => ['products']]);

        echo "\n✓ Step 2: Admin dapat akses halaman product management";

        // === STEP 3: Admin add product ===
        $productData = [
            'nama_produk' => 'Beras Organik Premium',
            'deskripsi' => 'Beras organik berkualitas tinggi',
            'harga' => 50000,
            'stok' => 100,
            'kategori' => 'beras',
            // Skip gambar upload karena butuh GD extension
        ];

        $addResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/admin/products', $productData);

        $addResponse->assertStatus(201)
            ->assertJson(['message' => 'Product created successfully!'])
            ->assertJsonStructure(['product' => ['id_produk', 'nama_produk']]);

        $newProductId = $addResponse->json('product.id_produk');
        echo "\n✓ Step 3: Admin berhasil tambah product (ID: {$newProductId}, Nama: Beras Organik Premium)";

        // 💡 Uncomment untuk liat response JSON:
        // $addResponse->dump();

        // Verify product ada di database
        $this->assertDatabaseHas('products', [
            'id_produk' => $newProductId,
            'nama_produk' => 'Beras Organik Premium',
            'harga' => 50000,
        ]);

        // === STEP 4: Admin edit product ===
        // ⚠️ MANUAL: Ganti $editProductId jika mau test dengan ID tertentu
        $editProductId = $newProductId; // Default: edit product yang baru dibuat

        $updateData = [
            'nama_produk' => 'Beras Organik Premium UPDATED',
            'deskripsi' => 'Beras organik berkualitas tinggi - EDITED',
            'harga' => 55000, // Harga dinaikkan
            'stok' => 150,
            'kategori' => 'beras',
        ];

        $editResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->putJson("/api/admin/products/{$editProductId}", $updateData);

        $editResponse->assertStatus(200)
            ->assertJson(['message' => 'Product updated successfully!'])
            ->assertJsonPath('product.nama_produk', 'Beras Organik Premium UPDATED')
            ->assertJsonPath('product.harga', '55000.00'); // Harga dari DB format decimal

        echo "\n✓ Step 4: Admin berhasil edit product ID {$editProductId}";
        echo "\n  - Nama: 'Beras Organik Premium' → 'Beras Organik Premium UPDATED'";
        echo "\n  - Harga: Rp 50.000 → Rp 55.000";

        // Verify changes di database
        $this->assertDatabaseHas('products', [
            'id_produk' => $editProductId,
            'nama_produk' => 'Beras Organik Premium UPDATED',
            'harga' => 55000,
        ]);

        // === STEP 5: Admin hapus product ===
        // ⚠️ MANUAL: Ganti $deleteProductId jika mau test dengan ID tertentu
        $deleteProductId = $newProductId; // Default: hapus product yang baru dibuat

        $deleteResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/admin/products/{$deleteProductId}");

        $deleteResponse->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully!']);

        echo "\n✓ Step 5: Admin berhasil hapus product ID {$deleteProductId}";

        // Verify product sudah terhapus dari database
        $this->assertDatabaseMissing('products', [
            'id_produk' => $deleteProductId,
        ]);

        echo "\n\n✅ TC-INT-04: ALL TESTS PASSED";
        echo "\n\n💡 Tips: Untuk test dengan ID spesifik, edit variabel:";
        echo "\n   - \$editProductId (line ~75)";
        echo "\n   - \$deleteProductId (line ~100)\n";
    }
}
