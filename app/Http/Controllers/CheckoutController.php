<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // === PROSES CHECKOUT API (bulk submit) ===
    public function processCheckout(Request $request)
    {
        $request->validate([
            'id_user'            => 'required|exists:users,id_user',
            'id_alamat'          => 'required|exists:addresses,id_alamat',
            'metode_pembayaran'  => 'required|in:transfer_bank,ewallet,cod,kartu_kredit'
        ]);

        try {
            DB::beginTransaction();

            $cart = Cart::where('id_user', $request->id_user)->first();
            if (!$cart) {
                return response()->json(['error' => 'Keranjang tidak ditemukan'], 404);
            }

            $cartItems = CartDetail::where('id_keranjang', $cart->id_keranjang)
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Keranjang kosong'], 400);
            }

            $totalHarga = 0;
            foreach ($cartItems as $item) {
                $prod = $item->product;
                if (!$prod) {
                    return response()->json(['error' => 'Produk tidak ditemukan'], 400);
                }
                if ($prod->stok < $item->jumlah) {
                    return response()->json([
                        'error' => "Stok {$prod->nama_produk} tidak mencukupi"
                    ], 400);
                }
                // Gunakan harga produk atau kolom harga_satuan jika ada
                $hargaSatuan = $item->harga_satuan ?? $prod->harga;
                $totalHarga += $item->jumlah * $hargaSatuan;
            }

            $order = Order::create([
                'id_user'     => $request->id_user,
                'status'      => 'pending',
                'total_harga' => $totalHarga,
                'id_alamat'   => $request->id_alamat
            ]);

            foreach ($cartItems as $item) {
                $prod = $item->product;
                $hargaSatuan = $item->harga_satuan ?? $prod->harga;

                OrderDetail::create([
                    'id_pemesanan' => $order->id_pemesanan,
                    'id_produk'    => $item->id_produk,
                    'jumlah'       => $item->jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal'     => $item->jumlah * $hargaSatuan
                ]);

                $prod->decrement('stok', $item->jumlah);
            }

            Payment::create([
                'id_pemesanan'       => $order->id_pemesanan,
                'metode_pembayaran'  => $request->metode_pembayaran,
                'jumlah'             => $totalHarga,
                'status_pembayaran'  => 'pending'
            ]);

            CartDetail::where('id_keranjang', $cart->id_keranjang)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Checkout berhasil',
                'order'   => $order->load(['orderDetails.product', 'payment'])
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

    // === SINGLE CHECKOUT VIEW ===
    public function show($id_produk, Request $request)
    {
        $qty = max(1, (int)$request->query('qty', 1));
        $product = Product::where('id_produk', $id_produk)->firstOrFail();
        $user = Auth::user();

        if (!filled($user->alamat)) {
            return redirect()->route('profile.show', [
                'checkout'  => 1,
                'id_produk' => $product->id_produk,
                'qty'       => $qty,
            ])->with('need_address', true);
        }

        $hargaProduk = (int)$product->harga;
        $biayaAdmin  = 5000;
        $biayaOngkir = 20000;
        $subtotal    = $hargaProduk * $qty;
        $total       = $subtotal + $biayaAdmin + $biayaOngkir;

        $defaultAddress = Address::where('id_user', $user->id_user)
            ->where('is_default', true)
            ->first() ?: Address::where('id_user', $user->id_user)->first();

        $orderId = $request->session()->get('_order_id');

        return Inertia::render('User/Checkout', [
            'order_id' => $orderId,
            'user' => [
                'id_user'   => $user->id_user ?? $user->id ?? null,
                'id_alamat' => $defaultAddress?->id_alamat,
                'nama'      => $user->nama ?? $user->username ?? '',
                'email'     => $user->email ?? '',
                'no_telp'   => $user->no_telp ?? '',
                'alamat'    => $defaultAddress
                    ? "{$defaultAddress->alamat_lengkap}, {$defaultAddress->kota}, {$defaultAddress->provinsi} {$defaultAddress->kode_pos}"
                    : ($user->alamat ?? ''),
            ],
            'shipping' => [
                'name'    => $defaultAddress?->nama_penerima ?? ($user->nama ?? $user->username ?? ''),
                'text'    => $defaultAddress
                    ? "{$defaultAddress->alamat_lengkap}\n{$defaultAddress->kota}, {$defaultAddress->provinsi} {$defaultAddress->kode_pos}"
                    : ($user->alamat ?? 'Belum ada alamat'),
                'phone'   => $defaultAddress?->no_telp_penerima ?? ($user->no_telp ?? ''),
                'is_temp' => !$defaultAddress,
            ],
            'product' => [
                'id_produk'   => $product->id_produk,
                'nama_produk' => $product->nama_produk,
                'deskripsi'   => $product->deskripsi,
                'harga'       => $hargaProduk,
                'gambar_url'  => $this->getProductImageUrl($product->gambar),
            ],
            'qty' => $qty,
            'summary' => [
                'admin'    => $biayaAdmin,
                'ongkir'   => $biayaOngkir,
                'subtotal' => $subtotal,
                'total'    => $total,
            ],
            'items' => [] // memastikan props ada
        ]);
    }

    // === FORM ALAMAT (opsional) ===
    public function showAddressForm($id_produk, Request $request)
    {
        $qty = max(1, (int)$request->query('qty', 1));
        $product = Product::findOrFail($id_produk);

        return Inertia::render('User/CheckoutAddress', [
            'product' => $product,
            'qty'     => $qty,
            'user'    => Auth::user(),
        ]);
    }

    // === BULK CHECKOUT VIEW ===
    public function cartCheckout(Request $request)
    {
        Log::info('cartCheckout hit', ['user' => Auth::id()]);

        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $cart = Cart::where('id_user', $user->id_user)->first();
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $cartItemsRaw = CartDetail::where('id_keranjang', $cart->id_keranjang)
            ->with('product')
            ->get();

        if ($cartItemsRaw->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // Map item
        $cartItems = $cartItemsRaw->map(function ($row) {
            $prod = $row->product;
            return [
                'id_detail_keranjang' => $row->id_detail_keranjang,
                'id_produk'           => $row->id_produk,
                'qty'                 => $row->jumlah,
                'product'             => [
                    'id_produk'   => $prod->id_produk,
                    'nama_produk' => $prod->nama_produk,
                    'deskripsi'   => $prod->deskripsi,
                    'harga'       => (int)$prod->harga,
                    'gambar'      => $prod->gambar,
                    'gambar_url'  => $this->getProductImageUrl($prod->gambar),
                ],
            ];
        });

        $defaultAddress = Address::where('id_user', $user->id_user)
            ->where('is_default', true)
            ->first() ?: Address::where('id_user', $user->id_user)->first();

        $subtotal    = $cartItems->reduce(fn($s, $it) => $s + ($it['product']['harga'] * $it['qty']), 0);
        $biayaAdmin  = 5000;
        $biayaOngkir = 20000;
        $total       = $subtotal + $biayaAdmin + $biayaOngkir;

        return Inertia::render('User/Checkout', [
            'order_id' => null,
            'user' => [
                'id_user'   => $user->id_user ?? $user->id ?? null,
                'id_alamat' => $defaultAddress?->id_alamat,
                'nama'      => $user->nama ?? $user->username ?? '',
                'email'     => $user->email ?? '',
                'no_telp'   => $user->no_telp ?? '',
                'alamat'    => $defaultAddress
                    ? "{$defaultAddress->alamat_lengkap}, {$defaultAddress->kota}, {$defaultAddress->provinsi} {$defaultAddress->kode_pos}"
                    : ($user->alamat ?? ''),
            ],
            'shipping' => [
                'name'    => $defaultAddress?->nama_penerima ?? ($user->nama ?? $user->username ?? ''),
                'text'    => $defaultAddress
                    ? "{$defaultAddress->alamat_lengkap}\n{$defaultAddress->kota}, {$defaultAddress->provinsi} {$defaultAddress->kode_pos}"
                    : ($user->alamat ?? 'Belum ada alamat'),
                'phone'   => $defaultAddress?->no_telp_penerima ?? ($user->no_telp ?? ''),
                'is_temp' => !$defaultAddress,
            ],
            'product' => null,
            'qty'     => 0,
            'items'   => $cartItems,
            'summary' => [
                'admin'    => $biayaAdmin,
                'ongkir'   => $biayaOngkir,
                'subtotal' => $subtotal,
                'total'    => $total,
            ],
        ]);
    }

    private function getProductImageUrl($gambar)
    {
        if (!$gambar) {
            return asset('/assets/dashboard/profil.png');
        }
        $clean = str_replace('\\', '/', trim($gambar));
        $clean = preg_replace('#^/?(public|storage)/#', '', $clean);
        if (preg_match('#^https?://#i', $clean)) {
            return $clean;
        }
        return asset('storage/' . $clean);
    }
}