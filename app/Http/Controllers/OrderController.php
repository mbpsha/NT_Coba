<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderController extends Controller
{
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
                        'kota' => $order->address->kota,
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

            Log::info('Validating request...', ['request_data' => $request->all()]);

            $validated = $request->validate([
                'qty' => 'required|integer|min:1',
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            // Get product and calculate total
            $product = \App\Models\Product::findOrFail($id_produk);
            $qty = $validated['qty'];
            $hargaProduk = (int) $product->harga;
            $biayaAdmin = 5000;
            $biayaOngkir = 20000;
            $total = ($hargaProduk * $qty) + $biayaAdmin + $biayaOngkir;

            Log::info('Product found and total calculated', ['product_id' => $product->id_produk, 'total' => $total]);

            // Get user's default address
            $defaultAddress = \App\Models\Address::where('id_user', $user->id_user)
                ->where('is_default', true)
                ->first();

            if (!$defaultAddress) {
                $defaultAddress = \App\Models\Address::where('id_user', $user->id_user)->first();
            }

            Log::info('Address check', ['has_address' => !!$defaultAddress]);

            // Jika tidak ada alamat di table addresses, create dari user.alamat
            if (!$defaultAddress && !empty($user->alamat)) {
                Log::info('Creating address from user.alamat', ['alamat' => $user->alamat]);

                $defaultAddress = \App\Models\Address::create([
                    'id_user' => $user->id_user,
                    'label' => 'Rumah',
                    'nama_penerima' => $user->nama ?? $user->username,
                    'no_telp_penerima' => $user->no_telp ?? '',
                    'alamat_lengkap' => $user->alamat,
                    'kota' => 'Tidak diketahui',
                    'provinsi' => 'Tidak diketahui',
                    'kode_pos' => '00000',
                    'is_default' => true,
                ]);

                Log::info('Address created from user.alamat', ['address_id' => $defaultAddress->id_alamat]);
            }

            if (!$defaultAddress) {
                Log::warning('No address found and user.alamat is empty', ['user_id' => $user->id_user]);
                return back()->withErrors(['error' => 'Alamat belum dilengkapi. Silakan lengkapi alamat di halaman profil terlebih dahulu.']);
            }
        } catch (\Exception $e) {
            Log::error('Error in createFromCheckout before transaction', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }

        try {
            DB::beginTransaction();

            Log::info('Creating order...', ['user_id' => $user->id_user, 'address_id' => $defaultAddress->id_alamat]);

            // Create order
            $order = Order::create([
                'id_user' => $user->id_user,
                'id_alamat' => $defaultAddress->id_alamat,
                'total_harga' => $total,
                'status' => 'pending' // Status pending, menunggu konfirmasi pembayaran
            ]);

            Log::info('Order created', ['order_id' => $order->id_order]);

            // Create order detail
            \App\Models\OrderDetail::create([
                'id_order' => $order->id_order,
                'id_produk' => $id_produk,
                'jumlah' => $qty,
                'harga' => $hargaProduk
            ]);

            Log::info('Order detail created');

            // PAYMENT AKAN DIBUAT SAAT USER UPLOAD BUKTI TRANSFER (di PaymentController)
            // Tidak create payment di sini

            DB::commit();

            Log::info('Transaction committed successfully - Order created without payment');

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
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage()]);
        }
    }
}