<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-04 STEP 4: Admin Edit Product
 * Test admin bisa mengedit product yang ada
 */
class Step4_EditProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_edit_product(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        // Setup: Bikin product yang akan diedit
        $product = Product::factory()->create([
            'nama_produk' => 'Beras Organik Premium',
            'harga' => 50000,
            'stok' => 100,
        ]);

        echo "\n=== TC-INT-04 STEP 4: Admin Edit Product ===\n";
        echo "Product ID: {$product->id_produk}\n";
        echo "Nama awal: {$product->nama_produk}\n";
        echo "Harga awal: Rp " . number_format($product->harga, 0, ',', '.') . "\n\n";

        // Data update
        $updateData = [
            'nama_produk' => 'Beras Organik Premium UPDATED',
            'deskripsi' => 'Beras organik berkualitas tinggi - EDITED',
            'harga' => 55000, // Harga dinaikkan
            'stok' => 150,
            'kategori' => 'beras',
        ];

        // Edit product
        $editResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/admin/products/{$product->id_produk}", $updateData);

        // Cek update berhasil
        $editResponse->assertStatus(200)
            ->assertJson(['message' => 'Product updated successfully!'])
            ->assertJsonPath('product.nama_produk', 'Beras Organik Premium UPDATED')
            ->assertJsonPath('product.harga', '55000.00');

        echo "✓ Admin berhasil edit product ID {$product->id_produk}\n";
        echo "  - Nama: 'Beras Organik Premium' → 'Beras Organik Premium UPDATED'\n";
        echo "  - Harga: Rp 50.000 → Rp 55.000\n";
        echo "  - Stok: 100 → 150\n";

        // 💡 Uncomment untuk liat response JSON:
        // $editResponse->dump();

        // Verify changes di database
        $this->assertDatabaseHas('products', [
            'id_produk' => $product->id_produk,
            'nama_produk' => 'Beras Organik Premium UPDATED',
            'harga' => 55000,
        ]);

        echo "✓ Perubahan berhasil tersimpan di database\n";

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
