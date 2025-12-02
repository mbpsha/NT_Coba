<?php

namespace Tests\Feature\Product;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-04 STEP 5: Admin Delete Product
 * Test admin bisa menghapus product
 */
class Step5_DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_delete_product(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        // Setup: Bikin product yang akan dihapus
        $product = Product::factory()->create([
            'nama_produk' => 'Beras Organik Premium',
            'harga' => 50000,
            'stok' => 100,
        ]);

        echo "\n=== TC-INT-04 STEP 5: Admin Delete Product ===\n";
        echo "Product ID: {$product->id_produk}\n";
        echo "Nama: {$product->nama_produk}\n\n";

        // Delete product
        $deleteResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/admin/products/{$product->id_produk}");

        // Cek delete berhasil
        $deleteResponse->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully!']);

        echo "✓ Admin berhasil hapus product ID {$product->id_produk}\n";

        // 💡 Uncomment untuk liat response JSON:
        // $deleteResponse->dump();

        // Verify product sudah terhapus dari database
        $this->assertDatabaseMissing('products', [
            'id_produk' => $product->id_produk,
        ]);

        echo "✓ Product berhasil dihapus dari database\n";

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
