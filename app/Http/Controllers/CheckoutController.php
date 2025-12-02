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
use Illuminate\Support\Facades\Log;

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

    protected function buildShippingQuote(?Address $address, int $itemCount): array
    {
        $weight = max(1, $itemCount) * max(1, $this->defaultItemWeight);
        $quote  = $this->shippingService->quote($address, $weight);
        $quote['weight'] = $weight;

        return $quote;
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'id_alamat' => 'required|exists:addresses,id_alamat',
            'metode_pembayaran' => 'required|in:transfer_bank,ewallet,cod,kartu_kredit'
        ]);

        try {
            DB::beginTransaction();

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

            $address       = Address::findOrFail($request->id_alamat);
            $shippingQuote = $this->buildShippingQuote($address, $totalItems);
            $grandTotal    = $subtotal + $this->adminFee + $shippingQuote['cost'];

            $order = Order::create([
                'id_user' => $request->id_user,
                'status' => 'pending',
                'total_harga' => $grandTotal,
                'id_alamat' => $address->id_alamat,
                'shipping_cost' => $shippingQuote['cost'],
                'admin_fee' => $this->adminFee,
                'shipping_weight' => $shippingQuote['weight'],
                'shipping_destination_city_id' => $shippingQuote['destination_city_id'],
                'shipping_courier' => $shippingQuote['courier'],
                'shipping_service' => $shippingQuote['service'],
                'shipping_etd' => $shippingQuote['etd'],
                'shipping_is_estimated' => $shippingQuote['is_estimated'],
            ]);

            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'id_order' => $order->id_order,
                    'id_produk' => $item->id_produk,
                    'jumlah' => $item->jumlah,
                    'harga' => $item->harga_satuan,
                ]);

                $item->product->decrement('stok', $item->jumlah);
            }

            $payment = Payment::create([
                'id_order' => $order->id_order,
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah' => $grandTotal,
                'status' => 'pending'
            ]);

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
        $subtotal    = $hargaProduk * $qty;

        $defaultAddress = Address::where('id_user', $user->id_user)
            ->where('is_default', true)
            ->first();

        // Cek apakah user punya alamat default di table addresses
        if (!$defaultAddress) {
            $defaultAddress = Address::where('id_user', $user->id_user)->first();
        }

        $shippingQuote = $this->buildShippingQuote($defaultAddress, $qty);
        $biayaAdmin    = $this->adminFee;
        $biayaOngkir   = $shippingQuote['cost'];
        $total         = $subtotal + $biayaAdmin + $biayaOngkir;

        // Ambil order_id dari session kalau ada (hanya untuk web, bukan API)
        $orderId = null;
        if ($request->hasSession()) {
            $orderId = $request->session()->get('_order_id');
        }

        $checkoutData = [
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
                    ? $defaultAddress->alamat_lengkap
                    : ($user->alamat ?? 'Belum ada alamat'),
                'phone' => $defaultAddress
                    ? $defaultAddress->no_telp_penerima
                    : ($user->no_telp ?? ''),
                'is_temp' => !$defaultAddress, // true jika pakai alamat sementara dari user.alamat
                'quote' => $shippingQuote,
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
                'weight'   => $shippingQuote['weight'],
                'courier'  => $shippingQuote['courier'],
                'service'  => $shippingQuote['service'],
                'etd'      => $shippingQuote['etd'],
                'is_shipping_estimated' => $shippingQuote['is_estimated'],
            ],
        ];

        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($checkoutData);
        }

        return Inertia::render('User/Checkout', $checkoutData);
    }

    public function showAddressForm($id_produk, Request $request)
    {
        $qty = max(1, (int) $request->query('qty', 1));
        $product = Product::findOrFail($id_produk);
        $user    = Auth::user();

        $prefill = [
            'nama'   => $user->nama ?? $user->username ?? '',
            'phone'  => $user->no_telp ?? '',
            'prov_kab' => '',
            'street' => '',
            'detail' => '',
        ];

        $defaultAddress = Address::where('id_user', $user->id_user)
            ->where('is_default', true)
            ->first();

        if ($defaultAddress) {
            $prefill['nama']    = $defaultAddress->nama_penerima;
            $prefill['phone']   = $defaultAddress->no_telp_penerima;

            // Format: Provinsi, Kabupaten, Kecamatan, Kelurahan/Desa, Kode Pos
            $parts = [$defaultAddress->provinsi, $defaultAddress->kabupaten];
            if ($defaultAddress->kecamatan) $parts[] = $defaultAddress->kecamatan;
            if ($defaultAddress->kelurahan_desa) $parts[] = $defaultAddress->kelurahan_desa;
            $parts[] = $defaultAddress->kode_pos;

            $prefill['prov_kab'] = implode(', ', $parts);

            // Format street: Nama Jalan, No Rumah (bukan alamat_lengkap!)
            $streetParts = [];
            if ($defaultAddress->nama_jalan) $streetParts[] = $defaultAddress->nama_jalan;
            if ($defaultAddress->no_rumah) $streetParts[] = $defaultAddress->no_rumah;
            $prefill['street'] = implode(', ', $streetParts);
        }

        return Inertia::render('User/CheckoutAddress', [
            'product' => $product,
            'qty' => $qty,
            'id_produk' => (int) $id_produk,
            'prefill' => $prefill,
        ]);
    }

    public function saveAddress(Request $request, $id_produk)
    {
        Log::info('SaveAddress called', [
            'id_produk' => $id_produk,
            'request_data' => $request->all(),
            'user_id' => $request->user()?->id_user ?? 'not authenticated'
        ]);

        $qty  = max(1, (int) $request->input('qty', 1));
        $user = $request->user();

        if (!$user) {
            Log::error('SaveAddress: No authenticated user');
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'prov_kab' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'detail' => 'nullable|string|max:255',
            'qty' => 'nullable|integer|min:1',
        ]);

        Log::info('SaveAddress: Validation passed', ['validated' => $validated]);

        // Parse: Provinsi, Kabupaten, Kecamatan, Kelurahan/Desa, Kode Pos
        $parts = array_values(array_filter(array_map('trim', explode(',', $validated['prov_kab']))));
        $provinsi      = $parts[0] ?? 'Tidak diketahui';
        $kabupaten     = $parts[1] ?? 'Tidak diketahui';
        $kecamatan     = $parts[2] ?? null;
        $kelurahanDesa = $parts[3] ?? null;
        $kodePos       = $parts[4] ?? '00000';

        // Parse street: Nama Jalan, No/Gedung, Detail lainnya (dipisah koma)
        $streetParts = array_values(array_filter(array_map('trim', explode(',', $validated['street']))));
        $namaJalan = $streetParts[0] ?? null;
        $noRumah   = $streetParts[1] ?? null;
        $detailLain = array_slice($streetParts, 2); // sisa nya jadi detail

        // Format alamat lengkap yang rapi (Jl, No, Detail, Kel., Kec., Kab., Prov.)
        $alamatParts = [];

        // Nama Jalan
        if ($namaJalan) {
            $alamatParts[] = $namaJalan;
        }

        // No Rumah/Gedung
        if ($noRumah) {
            $alamatParts[] = $noRumah;
        }

        // Detail lainnya (RT/RW, Blok, dll)
        if (!empty($detailLain)) {
            $alamatParts[] = implode(', ', $detailLain);
        }

        // Detail tambahan dari field terpisah
        if (!empty($validated['detail'])) {
            $alamatParts[] = $validated['detail'];
        }

        // Kelurahan/Desa
        if ($kelurahanDesa) {
            $alamatParts[] = "Kel. {$kelurahanDesa}";
        }

        // Kecamatan
        if ($kecamatan) {
            $alamatParts[] = "Kec. {$kecamatan}";
        }

        // Kabupaten
        $alamatParts[] = $kabupaten;

        // Provinsi dan Kode Pos
        $alamatParts[] = "{$provinsi} {$kodePos}";

        $alamatLengkap = implode(', ', $alamatParts);

        // Jadikan alamat baru sebagai default (alamat lain otomatis nonaktif)
        $updatedCount = Address::where('id_user', $user->id_user)->update(['is_default' => false]);
        Log::info('SaveAddress: Set other addresses to non-default', ['updated_count' => $updatedCount]);

        $addressData = [
            'id_user' => $user->id_user,
            'label' => 'Alamat Checkout',
            'nama_penerima' => $validated['nama'],
            'no_telp_penerima' => $validated['phone'],
            'nama_jalan' => $namaJalan,
            'no_rumah' => $noRumah,
            'alamat_lengkap' => $alamatLengkap,
            'kelurahan_desa' => $kelurahanDesa,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
            'provinsi' => $provinsi,
            'kode_pos' => $kodePos ?: '00000',
            'is_default' => true,
        ];

        Log::info('SaveAddress: About to create address', ['data' => $addressData]);

        try {
            $address = Address::create($addressData);
            Log::info('SaveAddress: Address created successfully', [
                'address_id' => $address->id_alamat,
                'address_exists_in_db' => Address::where('id_alamat', $address->id_alamat)->exists()
            ]);
        } catch (\Exception $e) {
            Log::error('SaveAddress: Failed to create address', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        // Return JSON for API requests (Postman/mobile apps)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Alamat berhasil disimpan!',
                'address' => $address,
                'redirect_url' => route('checkout.show', ['id_produk' => $id_produk, 'qty' => $qty])
            ], 200);
        }

        // Redirect for web requests (Inertia/browser)
        return redirect()
            ->route('checkout.show', ['id_produk' => $id_produk, 'qty' => $qty])
            ->with('message', 'Alamat berhasil disimpan! Silakan lanjutkan checkout.');
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
