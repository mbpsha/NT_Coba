<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
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

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Checkout gagal: ' . $e->getMessage()], 500);
        }
    }

    public function getOrderTracking($orderId)
    {
        $order = Order::with(['orderDetails.product', 'payment', 'user'])
                     ->findOrFail($orderId);
        return response()->json($order);
    }
}
