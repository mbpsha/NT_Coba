<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Payment;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected ShippingService $shippingService;
    protected int $adminFee;
    protected int $defaultItemWeight;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService   = $shippingService;
        $this->adminFee          = (int) config('rajaongkir.admin_fee', 5000);
        $this->defaultItemWeight = (int) config('rajaongkir.default_item_weight', 1000);
    }

    /**
     * Build shipping quote using ShippingService
     */
    protected function buildShippingQuote(?Address $address, int $itemCount): array
    {
        $weight = max(1, $itemCount) * max(1, $this->defaultItemWeight);

        $quote = $this->shippingService->quote($address, $weight);
        $quote['weight'] = $weight;

        return $quote;
    }

    /**
     * PROSES CHECKOUT
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'id_alamat' => 'required|exists:addresses,id_alamat',
            'metode_pembayaran' => 'required|in:transfer_bank,ewallet,cod,kartu_kredit',
        ]);

        try {
            DB::beginTransaction();

            // Ambil keranjang
            $cart = Cart::where('id_user', $request->id_user)
                ->with('cartDetails.product')
                ->first();

            if (!$cart) {
                return response()->json(['error' => 'Keranjang tidak ditemukan'], 404);
            }

            $cartItems = $cart->cartDetails;

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Keranjang kosong'], 400);
            }

            // Hitung subtotal
            $subtotal = 0;
            $totalItems = 0;

            foreach ($cartItems as $item) {
                if ($item->product->stok < $item->jumlah) {
                    return response()->json([
                        'error' => "Stok {$item->product->nama} tidak mencukupi"
                    ], 400);
                }

                $subtotal   += $item->jumlah * $item->harga_satuan;
                $totalItems += $item->jumlah;
            }

            // Alamat
            $address = Address::findOrFail($request->id_alamat);

            // Ongkir
            $shippingQuote = $this->buildShippingQuote($address, $totalItems);

            $grandTotal = $subtotal + $this->adminFee + $shippingQuote['cost'];

            // Buat order
            $order = Order::create([
                'id_user'      => $request->id_user,
                'status'       => 'pending',
                'total_harga'  => $grandTotal,
                'id_alamat'    => $address->id_alamat,
                'shipping_cost'=> $shippingQuote['cost'],
                'admin_fee'    => $this->adminFee,
                'shipping_weight' => $shippingQuote['weight'],
                'shipping_destination_city_id' => $shippingQuote['destination_city_id'],
                'shipping_courier' => $shippingQuote['courier'],
                'shipping_service' => $shippingQuote['service'],
                'shipping_etd'     => $shippingQuote['etd'],
                'shipping_is_estimated' => $shippingQuote['is_estimated'],
            ]);

            // Detail order
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'id_order'  => $order->id_order,
                    'id_produk' => $item->id_produk,
                    'jumlah'    => $item->jumlah,
                    'harga'     => $item->harga_satuan,
                ]);

                // Kurangi stok
                $item->product->decrement('stok', $item->jumlah);
            }

            // Pembayaran
            Payment::create([
                'id_order' => $order->id_order,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah' => $grandTotal,
                'status' => 'pending',
            ]);

            // Kosongkan keranjang
            CartDetail::where('id_keranjang', $cart->id_keranjang)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Checkout berhasil',
                'order'   => $order->load(['orderDetails.product', 'payment'])
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Checkout gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ORDER TRACKING
     */
    public function getOrderTracking($orderId)
    {
        $order = Order::with(['orderDetails.product', 'payment', 'user'])
            ->findOrFail($orderId);

        return response()->json($order);
    }

    /**
     * HALAMAN CHECKOUT PRODUK (Single Product Checkout)
     */
    public function show($id_produk, Request $request)
    {
        $qty = max(1, (int) $request->query('qty', 1));
        $product = Product::where('id_produk', $id_produk)->firstOrFail();
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // HARUS PUNYA ALAMAT
        if (!Address::where('id_user', $user->id_user)->exists()) {
            return redirect()
                ->route('profile.show', [
                    'checkout'  => 1,
                    'id_produk' => $product->id_produk,
                    'qty'       => $qty
                ])
                ->with('need_address', true);
        }

        // Alamat default
        $defaultAddress = Address::where('id_user', $user->id_user)
            ->where('is_default', true)
            ->first()
            ?: Address::where('id_user', $user->id_user)->first();

        // Shipping quote
        $shippingQuote = $this->buildShippingQuote($defaultAddress, $qty);

        $subtotal    = $product->harga * $qty;
        $biayaAdmin  = $this->adminFee;
        $biayaOngkir = $shippingQuote['cost'];
        $total       = $subtotal + $biayaAdmin + $biayaOngkir;

        return Inertia::render('User/Checkout', [
            // penting: kirim order_id dari flash agar tombol Konfirmasi dapat dipakai
            'order_id' => session('order_id') ?: null,

            'user' => [
                'id_user'   => $user->id_user,
                'id_alamat' => $defaultAddress->id_alamat ?? null,
                'nama'      => $user->nama ?? $user->username,
                'email'     => $user->email,
                'no_telp'   => $user->no_telp,
                'alamat'    => $defaultAddress->alamat_lengkap,
            ],

            'shipping' => [
                'name'  => $defaultAddress->nama_penerima,
                'text'  => $defaultAddress->alamat_lengkap,
                'phone' => $defaultAddress->no_telp_penerima,
                'quote' => $shippingQuote,
            ],

            'product' => [
                'id_produk'   => $product->id_produk,
                'nama_produk' => $product->nama_produk,
                'deskripsi'   => $product->deskripsi,
                'harga'       => $product->harga,
                'gambar_url'  => $this->getProductImageUrl($product->gambar),
            ],

            'qty' => $qty,

            'summary' => [
                'admin'   => $biayaAdmin,
                'ongkir'  => $biayaOngkir,
                'subtotal'=> $subtotal,
                'total'   => $total,
                'weight'  => $shippingQuote['weight'],
                'courier' => $shippingQuote['courier'],
                'service' => $shippingQuote['service'],
                'etd'     => $shippingQuote['etd'],
                'is_shipping_estimated'=> $shippingQuote['is_estimated'],
            ],
        ]);
    }

    /**
     * SHOW ADDRESS FORM (during checkout)
     */
    public function showAddressForm($id_produk, Request $request)
    {
        $qty = max(1, (int) $request->query('qty', 1));
        $user = Auth::user();

        $defaultAddress = Address::where('id_user', $user->id_user)
            ->where('is_default', true)->first();

        $prefill = [
            'nama'  => $defaultAddress->nama_penerima ?? $user->nama,
            'phone' => $defaultAddress->no_telp_penerima ?? $user->no_telp,
            'prov_kab' => '',
            'street' => '',
            'detail' => '',
        ];

        if ($defaultAddress) {
            $parts = [
                $defaultAddress->provinsi,
                $defaultAddress->kabupaten,
                $defaultAddress->kecamatan,
                $defaultAddress->kelurahan_desa,
                $defaultAddress->kode_pos,
            ];

            $prefill['prov_kab'] = implode(', ', array_filter($parts));

            $streetParts = [
                $defaultAddress->nama_jalan,
                $defaultAddress->no_rumah,
            ];

            $prefill['street'] = implode(', ', array_filter($streetParts));
        }

        return Inertia::render('User/CheckoutAddress', [
            'id_produk' => $id_produk,
            'qty' => $qty,
            'prefill' => $prefill,
        ]);
    }

    /**
     * SIMPAN ALAMAT CHECKOUT
     */
    public function saveAddress(Request $request, $id_produk)
    {
        $qty    = max(1, (int) $request->input('qty', 1));
        $user   = $request->user();

        $validated = $request->validate([
            'nama'   => 'required',
            'phone'  => 'required',
            'prov_kab' => 'required',
            'street' => 'required',
            'detail' => 'nullable',
            'qty'    => 'nullable|integer|min:1',
            'city_id' => 'nullable|integer', // TAMBAHAN: untuk shipping calculation
        ]);

        // Parse "Provinsi, Kabupaten, Kecamatan, Kelurahan, Kode Pos"
        $parts = array_map('trim', explode(',', $validated['prov_kab']));

        $provinsi  = $parts[0] ?? '';
        $kabupaten = $parts[1] ?? '';
        $kecamatan = $parts[2] ?? null;
        $kelurahan = $parts[3] ?? null;
        $kodePos   = $parts[4] ?? '00000';

        // Parse "Nama jalan, No rumah, detail"
        $streetParts = array_map('trim', explode(',', $validated['street']));
        $namaJalan = $streetParts[0] ?? null;
        $noRumah   = $streetParts[1] ?? null;
        $detailLain = implode(', ', array_slice($streetParts, 2));

        // Format alamat lengkap
        $alamatLengkap = trim(implode(', ', array_filter([
            $namaJalan,
            $noRumah,
            $detailLain,
            $validated['detail'] ?? null,
            "Kel. $kelurahan",
            "Kec. $kecamatan",
            $kabupaten,
            "$provinsi $kodePos",
        ])));

        // Set default address baru
        Address::where('id_user', $user->id_user)->update(['is_default' => false]);

        Address::create([
            'id_user' => $user->id_user,
            'label' => 'Alamat Checkout',
            'nama_penerima' => $validated['nama'],
            'no_telp_penerima'=> $validated['phone'],
            'nama_jalan' => $namaJalan,
            'no_rumah' => $noRumah,
            'alamat_lengkap' => $alamatLengkap,
            'kelurahan_desa' => $kelurahan,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
            'provinsi' => $provinsi,
            'kode_pos' => $kodePos,
            'city_id' => $validated['city_id'] ?? null, // TAMBAHAN: simpan city_id untuk shipping
            'is_default' => true,
        ]);

        return redirect()
            ->route('checkout.show', [
                'id_produk' => $id_produk,
                'qty' => $qty
            ])
            ->with('message', 'Alamat berhasil disimpan! Silakan lanjutkan checkout.');
    }

    /**
     * Helper: Convert gambar product ke URL
     */
    private function getProductImageUrl($gambar)
    {
        if (!$gambar) {
            return asset('/assets/dashboard/profil.png');
        }

        $clean = preg_replace('#^/?(public|storage)/#', '', str_replace('\\', '/', $gambar));

        if (preg_match('#^https?://#i', $clean)) {
            return $clean;
        }

        return asset('storage/' . $clean);
    }
}
