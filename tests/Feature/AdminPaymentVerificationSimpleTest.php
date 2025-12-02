<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Address;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TC-INT-03: Admin Payment Verification - Simplified Test
 *
 * Test Flow:
 * 1. Admin login
 * 2. Admin akses halaman payment verification
 * 3. Verifikasi payment yang belum diverifikasi (id_payment 1 di DB asli)
 * 4. Cek apakah status payment berubah jadi 'verified'
 */
class AdminPaymentVerificationSimpleTest extends TestCase
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
     * Test: Full flow TC-INT-03
     * 1. Admin login
     * 2. Admin akses payment verification page
     * 3. Verifikasi payment
     * 4. Cek status payment berubah
     */
    public function test_admin_payment_verification_flow(): void
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

        // Setup: Create payment yang pending
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

        echo "\n✓ Setup: Payment ID {$payment->id_payment} created with status 'pending'";

        // === STEP 2: Admin akses payment verification page ===
        $ordersResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/admin/orders');

        $ordersResponse->assertStatus(200)
            ->assertJsonStructure(['props' => ['orders']]);

        echo "\n✓ Step 2: Admin dapat akses halaman orders dengan payment pending";

        // 💡 Uncomment untuk liat response JSON:
        // $ordersResponse->dump();

        // === STEP 3: Verifikasi payment ===
        $verifyResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->putJson("/api/admin/payments/{$payment->id_payment}/verify", [
            'action' => 'approve',
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJson(['message' => 'Payment verification updated successfully!'])
            ->assertJsonPath('payment.status', 'verified');

        echo "\n✓ Step 3: Admin berhasil verifikasi payment ID {$payment->id_payment}";

        // 💡 Uncomment untuk liat response JSON:
        // $verifyResponse->dump();

        // === STEP 4: Cek status payment di database ===
        $payment->refresh();
        $this->assertEquals('verified', $payment->status);

        echo "\n✓ Step 4: Status payment berubah dari 'pending' → 'verified' di database";
        echo "\n\n✅ TC-INT-03: ALL TESTS PASSED\n";
    }
}
