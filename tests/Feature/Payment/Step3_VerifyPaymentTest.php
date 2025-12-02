<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Address;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-03 STEP 3: Admin Verifikasi Payment
 * Test admin bisa verifikasi payment dari pending → verified
 */
class Step3_VerifyPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_verify_payment(): void
    {
        // Setup: Bikin admin user
        $admin = User::factory()->admin()->create();

        // Login sebagai admin
        $loginResponse = $this->postJson('/api/login', [
            'login' => 'admin@admin.com',
            'password' => 'admin123',
        ]);

        $token = $loginResponse->json('token');

        // Setup: Bikin payment yang pending
        $user = User::factory()->create();
        $address = Address::factory()->create(['id_user' => $user->id_user]);
        $product = Product::factory()->create();
        $order = Order::factory()->create([
            'id_user' => $user->id_user,
            'id_alamat' => $address->id_alamat,
            'status' => 'pending',
        ]);
        $payment = Payment::factory()->create([
            'id_order' => $order->id_order,
            'status' => 'pending', // Belum diverifikasi
        ]);

        echo "\n=== TC-INT-03 STEP 3: Admin Verifikasi Payment ===\n";
        echo "Payment ID: {$payment->id_payment}\n";
        echo "Status awal: {$payment->status}\n\n";

        // Verifikasi payment
        $verifyResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->putJson("/api/admin/payments/{$payment->id_payment}/verify", [
            'action' => 'approve',
        ]);

        // Cek verifikasi berhasil
        $verifyResponse->assertStatus(200)
            ->assertJson(['message' => 'Payment verification updated successfully!'])
            ->assertJsonPath('payment.status', 'verified');

        echo "✓ Admin berhasil verifikasi payment ID {$payment->id_payment}\n";

        // 💡 Uncomment untuk liat response JSON:
        // $verifyResponse->dump();

        // Cek di database status benar-benar berubah
        $payment->refresh();
        $this->assertEquals('verified', $payment->status);

        echo "✓ Status payment berubah dari 'pending' → 'verified' di database\n";

        echo "\n=== Test Selesai ✓ ===\n";
    }
}
