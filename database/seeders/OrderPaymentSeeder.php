<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;

class OrderPaymentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1) Siapkan user non-admin (minimal 5)
            $users = User::where('role', '!=', 'admin')->take(5)->get();
            if ($users->count() < 5) {
                // fallback: ambil semua user non-admin yang ada
                $users = User::where('role', '!=', 'admin')->get();
                if ($users->isEmpty()) {
                    // kalau kosong banget, bikin 5 user cepat
                    $users = User::factory(5)->create(['role' => 'user']);
                }
            }

            // 2) Siapkan produk (minimal 6 item untuk variasi)
            if (Product::count() < 6) {
                Product::factory()->count(6)->create(); // kalau tak ada factory, silakan isi manual
            }
            $products = Product::inRandomOrder()->get();

            // 3) Siapkan alamat default untuk setiap user (kalau belum ada)
            foreach ($users as $u) {
                $addr = Address::where('id_user', $u->id_user)->first();
                if (!$addr) {
                    Address::create([
                        'id_user' => $u->id_user,
                        'label' => 'Rumah',
                        'nama_penerima' => $u->nama ?? 'Customer '.$u->id_user,
                        'no_telp_penerima' => '08123456789',
                        'alamat_lengkap' => 'Jl. Contoh No. '.$u->id_user,
                        'kabupaten' => 'Bandung',
                        'provinsi' => 'Jawa Barat',
                        'kode_pos' => '40112',
                        'is_default' => true,
                    ]);
                }
            }

            // 4) Buat beberapa order dengan status bervariasi
            $orderStatuses = ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
            $paymentStatuses = ['pending', 'verified', 'rejected'];

            // jumlah order yang dibuat
            $totalOrders = 12;

            for ($i = 0; $i < $totalOrders; $i++) {
                $user = $users->random();
                $address = Address::where('id_user', $user->id_user)->first();

                // buat order kosong dulu
                $order = Order::create([
                    'id_user' => $user->id_user,
                    'status' => Arr::random($orderStatuses),
                    'total_harga' => 0,
                    'id_alamat' => $address?->id_alamat,
                ]);

                // detail: 1-3 item random
                $detailCount = rand(1, 3);
                $picked = $products->random($detailCount);
                $total = 0;

                // Ensure $picked is always a collection
                $pickedCollection = is_a($picked, \Illuminate\Database\Eloquent\Collection::class) ? $picked : collect([$picked]);

                foreach ($pickedCollection as $p) {
                    $qty = rand(1, 3);
                    $line = $p->harga * $qty;
                    OrderDetail::create([
                        'id_order' => $order->id_order,
                        'id_produk' => $p->id_produk,
                        'jumlah' => $qty,
                        'harga' => $p->harga,
                    ]);
                    $total += $line;
                }

                // update total
                $order->update(['total_harga' => $total]);

                // payment: selalu buat, sesuaikan status
                $pStatus = Arr::random($paymentStatuses);

                Payment::create([
                    'id_order' => $order->id_order,
                    'metode_pembayaran' => 'qris',
                    'jumlah' => $total,
                    'status' => $pStatus,
                    'bukti_transfer' => $pStatus === 'verified'
                        ? '/storage/payment_proofs/sample.png'
                        : null,
                ]);

                // sinkronkan order bila payment verified (menyerupai logic controller)
                if ($pStatus === 'verified' && $order->status === 'pending') {
                    $order->update(['status' => 'diproses']);
                }
            }
        });
    }
}
