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
        $this->adminFee          = (int) config('services.rajaongkir.admin_fee', 5000);
        $this->defaultItemWeight = (int) config('services.rajaongkir.default_item_weight', 1000);
    }
    // Public API methods
    public function index()
    {
        return response()->json(Order::all());
    }

    public function show($id)
    {
        $order = Order::with(['user', 'address', 'orderDetails.product', 'payment'])
            ->findOrFail($id);

        return response()->json([
            'props' => [
                'order' => $order
            ]
        ]);
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

    /**
     * Store order via API (for Postman/Testing)
     * POST /api/orders
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            // Validate all required fields for API
            $validated = $request->validate([
                'id_produk' => 'required|integer|exists:products,id_produk',
                'id_alamat' => 'required|integer|exists:addresses,id_alamat',
                'shipping_cost' => 'required|integer|min:0',
                'admin_fee' => 'required|integer|min:0',
                'shipping_weight' => 'required|integer|min:1',
                'shipping_destination_city_id' => 'required|integer',
                'shipping_courier' => 'required|string',
                'shipping_service' => 'required|string',
                'shipping_etd' => 'required|string',
                'shipping_is_estimated' => 'required|integer|in:0,1',
                'quantity' => 'nullable|integer|min:1',
            ]);

            // Get product and calculate total
            $product = \App\Models\Product::findOrFail($validated['id_produk']);
            $qty = $validated['quantity'] ?? 1;
            $hargaProduk = (int) $product->harga;
            $subtotal = $hargaProduk * $qty;

            // Verify address belongs to user
            $address = Address::where('id_alamat', $validated['id_alamat'])
                ->where('id_user', $user->id_user)
                ->firstOrFail();

            // Calculate total
            $biayaAdmin = (int) $validated['admin_fee'];
            $biayaOngkir = (int) $validated['shipping_cost'];
            $total = $subtotal + $biayaAdmin + $biayaOngkir;

            // Create order
            $order = Order::create([
                'id_user' => $user->id_user,
                'id_alamat' => $validated['id_alamat'],
                'total_harga' => $total,
                'status' => 'pending',
                'admin_fee' => $biayaAdmin,
                'shipping_cost' => $biayaOngkir,
                'shipping_weight' => $validated['shipping_weight'],
                'shipping_destination_city_id' => $validated['shipping_destination_city_id'],
                'shipping_courier' => $validated['shipping_courier'],
                'shipping_service' => $validated['shipping_service'],
                'shipping_etd' => $validated['shipping_etd'],
                'shipping_is_estimated' => $validated['shipping_is_estimated'],
            ]);

            // Create order detail
            \App\Models\OrderDetail::create([
                'id_order' => $order->id_order,
                'id_produk' => $validated['id_produk'],
                'jumlah' => $qty,
                'harga' => $hargaProduk,
            ]);

            // Load relations
            $order->load(['orderDetails.product', 'address', 'user']);

            return response()->json([
                'message' => 'Order created successfully!',
                'order' => $order,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Order creation failed',
                'message' => $e->getMessage()
            ], 500);
        }
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
                        return [
                            'nama' => $detail->product->nama_produk ?? 'Produk tidak ditemukan',
                            'gambar' => $detail->product->gambar_url ?? '/assets/dashboard/profil.png',
                            'harga' => $detail->harga,
                            'jumlah' => $detail->jumlah,
                            'subtotal' => $detail->harga * $detail->jumlah
                        ];
                    }),
                    'payment_status' => $order->payment ? $order->payment->status : null,
                    'payment_method' => $order->payment ? $order->payment->metode_pembayaran : null,
                    'bukti_transfer' => $order->payment && $order->payment->bukti_transfer
                        ? asset('storage/' . $order->payment->bukti_transfer)
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
        try {
            Log::info('CreateFromCheckout called', ['id_produk' => $id_produk, 'user' => $request->user()->id_user ?? 'guest']);

            $user = $request->user();

            if (!$user) {
                Log::error('No authenticated user');
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            Log::info('Validating request...', ['request_data' => $request->all()]);

            // Validate all required fields from Postman
            $validated = $request->validate([
                'id_alamat' => 'required|integer|exists:addresses,id_alamat',
                'shipping_cost' => 'required|integer|min:0',
                'admin_fee' => 'required|integer|min:0',
                'shipping_weight' => 'required|integer|min:1',
                'shipping_destination_city_id' => 'required|integer',
                'shipping_courier' => 'required|string',
                'shipping_service' => 'required|string',
                'shipping_etd' => 'required|string',
                'shipping_is_estimated' => 'required|integer|in:0,1',
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            // Get product from URL parameter
            $product = \App\Models\Product::findOrFail($id_produk);
            $qty = 1; // Default qty 1, atau bisa ambil dari request jika ada
            $hargaProduk = (int) $product->harga;
            $subtotal = $hargaProduk * $qty;

            Log::info('Product found', ['product_id' => $product->id_produk, 'qty' => $qty, 'harga' => $hargaProduk]);

            // Get address
            $address = Address::where('id_alamat', $validated['id_alamat'])
                ->where('id_user', $user->id_user)
                ->firstOrFail();

            Log::info('Address verified', ['address_id' => $address->id_alamat]);

            // Calculate total: product + admin_fee + shipping_cost
            $biayaAdmin = (int) $validated['admin_fee'];
            $biayaOngkir = (int) $validated['shipping_cost'];
            $total = $subtotal + $biayaAdmin + $biayaOngkir;

            Log::info('Total calculated', [
                'subtotal' => $subtotal,
                'admin_fee' => $biayaAdmin,
                'shipping_cost' => $biayaOngkir,
                'total' => $total,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in createFromCheckout before transaction', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        try {
            DB::beginTransaction();

            Log::info('Creating order...', ['user_id' => $user->id_user, 'address_id' => $address->id_alamat]);

            // Create order with all shipping details
            $order = Order::create([
                'id_user' => $user->id_user,
                'id_alamat' => $validated['id_alamat'],
                'total_harga' => $total,
                'shipping_cost' => $validated['shipping_cost'],
                'admin_fee' => $validated['admin_fee'],
                'shipping_weight' => $validated['shipping_weight'],
                'shipping_destination_city_id' => $validated['shipping_destination_city_id'],
                'shipping_courier' => strtoupper($validated['shipping_courier']),
                'shipping_service' => strtoupper($validated['shipping_service']),
                'shipping_etd' => $validated['shipping_etd'],
                'shipping_is_estimated' => $validated['shipping_is_estimated'],
                'status' => 'pending' // Status pending, menunggu konfirmasi pembayaran
            ]);

            Log::info('Order created successfully', [
                'order_id' => $order->id_order,
                'order_exists_in_db' => Order::where('id_order', $order->id_order)->exists()
            ]);

            // Create order detail
            \App\Models\OrderDetail::create([
                'id_order' => $order->id_order,
                'id_produk' => $id_produk, // Ambil dari URL parameter bukan dari $validated
                'jumlah' => $qty,
                'harga' => $hargaProduk
            ]);

            Log::info('Order detail created successfully');

            // PAYMENT AKAN DIBUAT SAAT USER UPLOAD BUKTI TRANSFER (di PaymentController)
            // Tidak create payment di sini

            DB::commit();

            Log::info('Transaction committed successfully - Order created without payment');

            // Return JSON for API requests
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Pesanan berhasil dibuat!',
                    'order' => $order->load(['user', 'address', 'orderDetails.product']),
                    'order_id' => $order->id_order,
                    'total_harga' => $total,
                ], 200);
            }

            // Redirect ke halaman checkout dengan order_id di URL
            return redirect()->route('checkout.show', ['id_produk' => $id_produk])
                ->with([
                    'order_created' => true,
                    'order_id' => $order->id_order,
                    'total_harga' => $total,
                    'message' => 'Pesanan berhasil dibuat! Silakan upload bukti pembayaran.'
                ])
                ->with('_order_id', $order->id_order); // Simpan di session juga

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction rollback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage()]);
        }
    }
}
