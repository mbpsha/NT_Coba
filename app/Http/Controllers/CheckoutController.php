<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function processCheckout(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'id_alamat' => 'required|exists:addresses,id_alamat',
            'metode_pembayaran' => 'required|in:transfer_bank,ewallet,cod,kartu_kredit'
        ]);

        try {
            DB::beginTransaction();

            // Ambil keranjang user
            $cart = Cart::where('id_user', $request->id_user)->first();
            if (!$cart) {
                return response()->json(['error' => 'Keranjang tidak ditemukan'], 404);
            }

            // Ambil item keranjang
            $cartItems = CartDetail::where('id_keranjang', $cart->id_keranjang)
                                ->with('product')
                                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Keranjang kosong'], 400);
            }

            // Validasi stok dan hitung total
            $totalHarga = 0;
            foreach ($cartItems as $item) {
                if ($item->product->stok < $item->jumlah) {
                    return response()->json([
                        'error' => "Stok {$item->product->nama} tidak mencukupi"
                    ], 400);
                }
                $totalHarga += $item->jumlah * $item->harga_satuan;
            }

            // Buat order
            $order = Order::create([
                'id_user' => $request->id_user,
                'status' => 'pending',
                'total_harga' => $totalHarga,
                'id_alamat' => $request->id_alamat
            ]);

            // Buat order detail dan kurangi stok
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'id_pemesanan' => $order->id_pemesanan,
                    'id_produk' => $item->id_produk,
                    'jumlah' => $item->jumlah,
                    'harga_satuan' => $item->harga_satuan,
                    'subtotal' => $item->jumlah * $item->harga_satuan
                ]);

                // Kurangi stok produk
                $item->product->decrement('stok', $item->jumlah);
            }

            // Buat payment record
            $payment = Payment::create([
                'id_pemesanan' => $order->id_pemesanan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah' => $totalHarga,
                'status_pembayaran' => 'pending'
            ]);

            // Hapus item dari keranjang
            CartDetail::where('id_keranjang', $cart->id_keranjang)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Checkout berhasil',
                'order' => $order->load(['orderDetails.product', 'payment'])
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Checkout gagal: '.$e->getMessage()], 500);
        }
    }

    public function getOrderTracking($orderId)
    {
        $order = Order::with(['orderDetails.product', 'payment', 'user'])->findOrFail($orderId);
        return response()->json($order);
    }

    public function show($id_produk, Request $request)
    {
        $qty = max(1, (int) $request->query('qty', 1));

        $product = Product::where('id_produk', $id_produk)->firstOrFail();
        $user = Auth::user();

        // Wajib punya alamat minimal (redirect ke profil bila kosong)
        if (!filled($user->alamat)) {
            return redirect()
                ->route('profile.show', [
                    'checkout'  => 1,
                    'id_produk' => $product->id_produk,
                    'qty'       => $qty,
                ])
                ->with('need_address', true);
        }

        // Normalisasi path gambar -> kirim URL final siap pakai
        $raw = (string) ($product->gambar ?? '');
        $clean = str_replace('\\', '/', trim($raw));
        $clean = preg_replace('#^/?(public|storage)/#', '', $clean); // buang prefix "public/" atau "storage/"
        $gambarUrl = $clean
            ? (preg_match('#^https?://#i', $clean) ? $clean : asset('storage/'.$clean))
            : asset('/assets/dashboard/profil.png');

        $hargaProduk = (int) $product->harga;
        $biayaAdmin  = 5000;
        $biayaOngkir = 20000;
        $subtotal    = $hargaProduk * $qty;
        $total       = $subtotal + $biayaAdmin + $biayaOngkir;

        // Cek apakah user punya alamat default di table addresses
        $defaultAddress = \App\Models\Address::where('id_user', $user->id_user)
            ->where('is_default', true)
            ->first();

        // Kalau ga ada, cari alamat pertama
        if (!$defaultAddress) {
            $defaultAddress = \App\Models\Address::where('id_user', $user->id_user)
                ->first();
        }

        // Ambil order_id dari session kalau ada (setelah create order)
        $orderId = $request->session()->get('_order_id');
        
        return Inertia::render('User/Checkout', [
            'order_id' => $orderId, // Pass order_id ke frontend
            'user' => [
                'id_user'  => $user->id_user ?? $user->id ?? null,
                'id_alamat'=> $defaultAddress ? $defaultAddress->id_alamat : null,
                'nama'     => $user->nama ?? $user->username ?? '',
                'email'    => $user->email ?? '',
                'no_telp'  => $user->no_telp ?? '',
                'alamat'   => $defaultAddress
                    ? "{$defaultAddress->alamat_lengkap}, {$defaultAddress->kota}, {$defaultAddress->provinsi} {$defaultAddress->kode_pos}"
                    : ($user->alamat ?? ''),
            ],
            'shipping' => [
                'name' => $defaultAddress
                    ? $defaultAddress->nama_penerima
                    : ($user->nama ?? $user->username ?? ''),
                'text' => $defaultAddress
                    ? "{$defaultAddress->alamat_lengkap}\n{$defaultAddress->kota}, {$defaultAddress->provinsi} {$defaultAddress->kode_pos}"
                    : ($user->alamat ?? 'Belum ada alamat'),
                'phone' => $defaultAddress
                    ? $defaultAddress->no_telp_penerima
                    : ($user->no_telp ?? ''),
                'is_temp' => !$defaultAddress, // true jika pakai alamat sementara dari user.alamat
            ],
            'product' => [
                'id_produk'   => $product->id_produk,
                'nama_produk' => $product->nama_produk,
                'deskripsi'   => $product->deskripsi,
                'harga'       => $hargaProduk,
                'gambar_url'  => $this->getProductImageUrl($product->gambar), // convert to full URL
            ],
            'qty' => $qty,
            'summary' => [
                'admin'    => $biayaAdmin,
                'ongkir'   => $biayaOngkir,
                'subtotal' => $subtotal,
                'total'    => $total,
            ],
        ]);
    }

    public function showAddressForm($id_produk, Request $request)
    {
        $qty = max(1, (int) $request->query('qty', 1));
        $product = Product::findOrFail($id_produk);

        return Inertia::render('User/CheckoutAddress', [
            'product' => $product,
            'qty' => $qty,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Helper method untuk convert gambar path ke URL
     */
    private function getProductImageUrl($gambar)
    {
        if (!$gambar) {
            return asset('/assets/dashboard/profil.png');
        }

        // Normalisasi path
        $clean = str_replace('\\', '/', trim($gambar));

        // Buang prefix "public/" atau "storage/" kalau ada
        $clean = preg_replace('#^/?(public|storage)/#', '', $clean);

        // Kalau sudah full URL, return aja
        if (preg_match('#^https?://#i', $clean)) {
            return $clean;
        }

        // Convert ke storage URL
        return asset('storage/' . $clean);
    }
}
