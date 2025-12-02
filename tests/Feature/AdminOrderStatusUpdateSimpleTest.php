<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderStatusUpdateSimpleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-INT-05: Admin Update Status Pesanan
     * Test flow: Login → Akses Orders Page → Update Status
     */
    public function test_admin_order_status_update_flow(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Setup: Bikin product untuk order
        $product = Product::factory()->create([
            'nama_produk' => 'Test Product for Order',
            'harga' => 50000,
            'stok' => 100,
        ]);

        // Setup: Bikin order dengan status 'pending'
        $order = Order::factory()->create([
            'id_user' => $admin->id_user, // User primary key: id_user
            'total_harga' => 50000,
            'status' => 'pending', // Kolom yang bener: 'status', bukan 'status_pesanan'
        ]);

        // Setup: Bikin payment untuk order ini
        Payment::factory()->create([
            'id_order' => $order->id_order,
            'jumlah' => 50000,
            'status' => 'verified',
        ]);

        echo "\n=== TC-INT-05: Admin Update Status Pesanan ===\n";
        echo "Order ID yang dibuat: {$order->id_order}\\n";
        echo "Status awal: {$order->status}\\n\\n";
        // STEP 1: Admin Login
        // ============================================================
        echo "STEP 1: Admin login...\n";
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        // Cek login berhasil dan dapet token
        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);

        $token = $loginResponse->json('token');
        echo "✓ Admin berhasil login (email: admin@admin.com)\n\n";

        // ============================================================
        // STEP 2: Admin akses halaman order management
        // ============================================================
        echo "STEP 2: Admin mengakses halaman order management...\n";
        $ordersResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/admin/orders');

        // Cek bisa akses halaman orders
        $ordersResponse->assertStatus(200)
            ->assertJsonStructure(['props' => ['orders']]);
        echo "✓ Halaman order management berhasil diakses\n";

        // 💡 Uncomment untuk liat response JSON:
        // $ordersResponse->dump();
        echo "\n";

        // ============================================================
        // STEP 3: Update status pesanan
        // ============================================================
        echo "STEP 3: Admin update status pesanan...\n";

        // 🔧 GANTI NILAI INI SESUAI KEBUTUHAN:
        $updateOrderId = $order->id_order;   // 👈 Bisa ganti manual kalo mau test order ID tertentu
        $newStatus = 'dikirim';              // 👈 GANTI STATUS: 'pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'

        echo "Order ID yang akan diupdate: {$updateOrderId}\n";
        echo "Status baru: {$newStatus}\n";

        $updateResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/admin/orders/{$updateOrderId}/status", [
            'status' => $newStatus, // Kolom yang bener: 'status', bukan 'status_pesanan'
        ]);

        // Cek update berhasil (API response)
        $updateResponse->assertStatus(200);
        echo "✓ Status pesanan berhasil diupdate\n";

        // 💡 Uncomment untuk liat response JSON:
        // $updateResponse->dump();

        // Cek di database status benar-benar berubah
        $this->assertDatabaseHas('orders', [
            'id_order' => $updateOrderId,
            'status' => $newStatus,
        ]);
        echo "✓ Status di database sudah berubah jadi '{$newStatus}'\n";

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
