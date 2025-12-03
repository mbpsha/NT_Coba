<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use App\Http\Requests\OrderRequest;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderController extends Controller
{
    protected ShippingService $shippingService;
    protected int $adminFee;
    protected int $defaultItemWeight;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService   = $shippingService;
        // konsisten dengan CheckoutController
        $this->adminFee          = (int) config('rajaongkir.admin_fee', 5000);
        $this->defaultItemWeight = (int) config('rajaongkir.default_item_weight', 1000);
    }

    // Public API methods
    public function index()
    {
        return response()->json(Order::all());
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    // Admin methods
    public function indexAdmin()
    {
        $orders = Order::with(['user', 'address', 'orderDetails.product'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/OrdersManagement', [
            'orders' => $orders
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,dikirim,selesai,dibatalkan'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    public function store(OrderRequest $request)
    {
        $order = Order::create($request->validated());
        return response()->json($order, 201);
    }

    public function update(OrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->validated());
        return response()->json($order);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }

    /**
     * Get user's orders (Pesanan Saya)
     * Fetch semua pesanan user dengan detail product, payment status, dan address
     */
    public function myOrders(Request $request)
    {
        $user = $request->user();

        $orders = Order::with([
            'orderDetails.product',
            'payment',
            'address'
        ])
            ->where('id_user', $user->id_user)
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id_order' => $order->id_order,
                    'tanggal' => $order->created_at->format('d M Y H:i'),
                    'status' => $order->status,
                    'total_harga' => $order->total_harga,
                    'admin_fee' => $order->admin_fee,
                    'shipping' => [
                        'cost' => $order->shipping_cost,
                        'courier' => $order->shipping_courier,
                        'service' => $order->shipping_service,
                        'etd' => $order->shipping_etd,
                        'weight' => $order->shipping_weight,
                        'is_estimated' => $order->shipping_is_estimated,
                    ],
                    'products' => $order->orderDetails->map(function ($detail) {
                        $gambar = $detail->product->gambar
                            ? asset('storage/' . ltrim($detail->product->gambar, '/'))
                            : asset('/assets/dashboard/profil.png');

                        return [
                            'nama' => $detail->product->nama_produk ?? 'Produk tidak ditemukan',
                            'gambar_url' => $gambar, // gunakan URL siap pakai
                            'harga' => $detail->harga,
                            'jumlah' => $detail->jumlah,
                            'subtotal' => $detail->harga * $detail->jumlah
                        ];
                    }),
                    'payment_status' => $order->payment ? $order->payment->status : null,
                    'payment_method' => $order->payment ? $order->payment->metode_pembayaran : null,
                    // konsisten dengan kolom pada tabel payments
                    'bukti_pembayaran' => $order->payment && $order->payment->bukti_pembayaran
                        ? asset('storage/' . ltrim($order->payment->bukti_pembayaran, '/'))
                        : null,
                    'alamat' => $order->address ? [
                        'nama_penerima' => $order->address->nama_penerima,
                        'no_telp' => $order->address->no_telp_penerima,
                        'alamat_lengkap' => $order->address->alamat_lengkap,
                        'kota' => $order->address->kabupaten,
                        'provinsi' => $order->address->provinsi,
                        'kode_pos' => $order->address->kode_pos,
                    ] : null
                ];
            });

        return Inertia::render('User/MyOrders', [
            'orders' => $orders
        ]);
    }

    /**
     * Create order from checkout page (User creates order before payment)
     */
    public function createFromCheckout(Request $request, $id_produk)
    {
        $user = $request->user();

        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
            'metode_pembayaran' => 'nullable|string|in:transfer_bank,ewallet,cod,kartu_kredit,qris',
        ]);

        $qty = (int) $validated['qty'];

        // Alamat default
        $defaultAddress = Address::where('id_user', $user->id_user)->where('is_default', true)->first()
            ?: Address::where('id_user', $user->id_user)->first();

        if (!$defaultAddress && !empty($user->alamat)) {
            $defaultAddress = Address::create([
                'id_user' => $user->id_user,
                'label' => 'Rumah',
                'nama_penerima' => $user->nama ?? $user->username,
                'no_telp_penerima' => $user->no_telp ?? '',
                'alamat_lengkap' => $user->alamat,
                'provinsi' => 'Tidak diketahui',
                'kabupaten' => 'Tidak diketahui',
                'kecamatan' => 'Tidak diketahui',
                'kelurahan_desa' => 'Tidak diketahui',
                'kode_pos' => '00000',
                'is_default' => true,
            ]);
        }

        if (!$defaultAddress) {
            return back()->withErrors(['error' => 'Alamat belum dilengkapi. Silakan lengkapi alamat terlebih dahulu.']);
        }

        try {
            DB::beginTransaction();

            // Kunci produk di dalam transaksi
            $product = \App\Models\Product::where('id_produk', $id_produk)
                ->lockForUpdate()
                ->firstOrFail();

            if ($product->stok < $qty) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Stok produk tidak mencukupi.']);
            }

            // Hitung biaya
            $hargaProduk   = (int) $product->harga;
            $subtotal      = $hargaProduk * $qty;
            $weight        = max(1, $qty) * max(1, $this->defaultItemWeight);
            $shippingQuote = $this->shippingService->quote($defaultAddress, $weight);
            $shippingQuote['weight'] = $weight;

            $biayaAdmin  = $this->adminFee;
            $biayaOngkir = $shippingQuote['cost'];
            $total       = $subtotal + $biayaAdmin + $biayaOngkir;

            // Buat order
            $order = Order::create([
                'id_user' => $user->id_user,
                'id_alamat' => $defaultAddress->id_alamat,
                'total_harga' => $total,
                'shipping_cost' => $biayaOngkir,
                'admin_fee' => $biayaAdmin,
                'shipping_weight' => $shippingQuote['weight'],
                'shipping_destination_city_id' => $shippingQuote['destination_city_id'] ?? null,
                'shipping_courier' => $shippingQuote['courier'] ?? null,
                'shipping_service' => $shippingQuote['service'] ?? null,
                'shipping_etd' => $shippingQuote['etd'] ?? null,
                'shipping_is_estimated' => $shippingQuote['is_estimated'] ?? false,
                'status' => 'pending',
            ]);

            // Detail order
            \App\Models\OrderDetail::create([
                'id_order' => $order->id_order,
                'id_produk' => $product->id_produk,
                'jumlah' => $qty,
                'harga' => $hargaProduk,
            ]);

            // Kurangi stok
            $product->decrement('stok', $qty);

            DB::commit();

            return redirect()->route('checkout.show', [
                'id_produk' => $id_produk,
                'qty'       => $qty,
            ])->with([
                'order_created' => true,
                'order_id' => $order->id_order,
                'total_harga' => $total,
                'message' => 'Pesanan berhasil dibuat! Silakan upload bukti pembayaran.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage()]);
        }
    }
}
