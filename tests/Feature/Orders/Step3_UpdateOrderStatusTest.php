<?php

namespace Tests\Feature\Orders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-05 STEP 3: Admin Update Order Status
 * Test admin bisa update status order
 */
class Step3_UpdateOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_update_order_status(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        // Setup: Bikin order dengan status 'pending'
        $product = Product::factory()->create([
            'nama_produk' => 'Test Product for Order',
            'harga' => 50000,
            'stok' => 100,
        ]);

        $order = Order::factory()->create([
            'id_user' => $admin->id_user,
            'total_harga' => 50000,
            'status' => 'pending',
        ]);

        Payment::factory()->create([
            'id_order' => $order->id_order,
            'jumlah' => 50000,
            'status' => 'verified',
        ]);

        echo "\n=== TC-INT-05 STEP 3: Admin Update Order Status ===\n";
        echo "Order ID: {$order->id_order}\n";
        echo "Status awal: {$order->status}\n\n";

        // 🔧 GANTI NILAI INI SESUAI KEBUTUHAN:
        $newStatus = 'dikirim'; // 👈 GANTI STATUS: 'pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'

        echo "Status baru: {$newStatus}\n";

        // Update status order
        $updateResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/admin/orders/{$order->id_order}/status", [
            'status' => $newStatus,
        ]);

        // Cek update berhasil
        $updateResponse->assertStatus(200);

        echo "✓ Admin berhasil update status order ID {$order->id_order}\n";

        // 💡 Uncomment untuk liat response JSON:
        // $updateResponse->dump();

        // Cek di database status benar-benar berubah
        $this->assertDatabaseHas('orders', [
            'id_order' => $order->id_order,
            'status' => $newStatus,
        ]);

        echo "✓ Status di database berubah dari 'pending' → '{$newStatus}'\n";

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
