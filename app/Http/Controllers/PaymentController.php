<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PaymentController extends Controller
{
    // Public API methods
    public function index()
    {
        return response()->json(Payment::all());
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return response()->json($payment);
    }

    // Admin methods
    public function indexAdmin()
    {
        $payments = Payment::with(['order.user', 'order.orderDetails.product', 'order.address'])
            ->latest()
            ->paginate(10);

        $payments->getCollection()->transform(function ($p) {
            $p->bukti_transfer_url = $p->bukti_transfer
                ? asset('storage/' . $p->bukti_transfer) // atau Storage::url($p->bukti_transfer)
                : null;
            return $p;
        });

        return Inertia::render('Admin/PaymentVerification', [
            'payments' => $payments
        ]);
    }

    public function verify(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,verified,rejected'
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update(['status' => $validated['status']]);

        // Update order status if payment is verified
        if ($validated['status'] === 'verified') {
            $order = Order::find($payment->id_order);
            if ($order && $order->status === 'pending') {
                $order->update(['status' => 'diproses']);
            }
        }

        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }

    public function store(PaymentRequest $request)
    {
        $payment = Payment::create($request->validated());
        return response()->json($payment, 201);
    }

    public function update(PaymentRequest $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update($request->validated());
        return response()->json($payment);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(null, 204);
    }

    /**
     * Get payment status for tracking
     */
    public function getPaymentStatus($id)
    {
        $payment = Payment::with('order')->findOrFail($id);

        return response()->json([
            'id_payment' => $payment->id_payment,
            'status' => $payment->status,
            'jumlah' => $payment->jumlah,
            'metode_pembayaran' => $payment->metode_pembayaran,
            'bukti_transfer' => $payment->bukti_transfer ? asset('storage/' . $payment->bukti_transfer) : null,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at,
            'shipping' => $payment->order ? [
                'cost' => $payment->order->shipping_cost,
                'courier' => $payment->order->shipping_courier,
                'service' => $payment->order->shipping_service,
                'etd' => $payment->order->shipping_etd,
            ] : null,
        ]);
    }

    /**
     * Confirm Payment - CREATE Payment dengan Upload Bukti Transfer
     * Payment baru dibuat saat user upload bukti, bukan saat order dibuat
     *
     * Menggunakan session untuk ambil order_id yang persistent
     */
    public function confirmPayment(Request $request, $id_order = null)
    {
        \Log::info('[PAYMENT_START] Payment confirmation started', [
            'url_order_id' => $id_order,
            'session_id' => session()->getId(),
            'session_order_id' => session('_order_id'),
            'session_last_order_id' => session('last_order_id'),
            'user_id' => $request->user()->id_user,
            'has_file' => $request->hasFile('bukti_transfer'),
            'file_size' => $request->hasFile('bukti_transfer') ? $request->file('bukti_transfer')->getSize() : null,
            'file_mime' => $request->hasFile('bukti_transfer') ? $request->file('bukti_transfer')->getMimeType() : null,
            'file_original_name' => $request->hasFile('bukti_transfer') ? $request->file('bukti_transfer')->getClientOriginalName() : null,
            'agree_value' => $request->input('agree'),
            'trx_id' => $request->input('trx_id'),
        ]);

        try {
            $validated = $request->validate([
                'trx_id' => 'nullable|string|max:100',
                'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
                'agree' => 'required|accepted'
            ]);

            \Log::info('[PAYMENT_VALIDATION_PASS] Validation passed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('[PAYMENT_VALIDATION_FAILED] Validation failed', [
                'errors' => $e->errors(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }

        try {
            // Ambil order_id dari URL parameter, session, atau flash data
            $orderId = $id_order ?? session('_order_id') ?? session('last_order_id');

            \Log::info('[PAYMENT_ORDER_ID] Order ID resolved', [
                'resolved_order_id' => $orderId,
                'source' => $id_order ? 'url' : (session('_order_id') ? 'session_order_id' : (session('last_order_id') ? 'session_last' : 'none')),
            ]);

            if (!$orderId) {
                \Log::error('[PAYMENT_ERROR] No order_id found in session or URL', [
                    'session_id' => session()->getId(),
                    'all_session_data' => session()->all(),
                ]);
                return back()->withErrors(['error' => 'Session order tidak ditemukan. Silakan buat pesanan terlebih dahulu.']);
            }

            // Cek apakah order ada
            \Log::info('[PAYMENT_CHECK_ORDER] Checking order existence', ['order_id' => $orderId]);
            $order = Order::find($orderId);

            if (!$order) {
                \Log::error('[PAYMENT_ERROR] Order not found', ['order_id' => $orderId]);
                return back()->withErrors(['error' => 'Order tidak ditemukan. Silakan buat pesanan baru.']);
            }

            \Log::info('[PAYMENT_ORDER_FOUND] Order found', ['order_id' => $order->id_order, 'order_user_id' => $order->id_user, 'current_user_id' => $request->user()->id_user]);

            // Validasi order milik user yang login (gunakan loose comparison untuk handle type mismatch)
            if ($order->id_user != $request->user()->id_user) {
                \Log::error('[PAYMENT_ERROR] Order ownership validation failed', [
                    'order_user_id' => $order->id_user,
                    'order_user_id_type' => gettype($order->id_user),
                    'current_user_id' => $request->user()->id_user,
                    'current_user_id_type' => gettype($request->user()->id_user),
                ]);
                return back()->withErrors(['error' => 'Order tidak valid.']);
            }

            // Cek apakah payment sudah ada
            \Log::info('[PAYMENT_CHECK_EXISTING] Checking existing payment', ['order_id' => $order->id_order]);
            $existingPayment = Payment::where('id_order', $order->id_order)->first();
            if ($existingPayment) {
                \Log::error('[PAYMENT_ERROR] Payment already exists', ['payment_id' => $existingPayment->id_payment, 'status' => $existingPayment->status]);
                return back()->withErrors(['error' => 'Pembayaran untuk pesanan ini sudah pernah diupload. Status: ' . $existingPayment->status]);
            }

            \Log::info('[PAYMENT_NO_EXISTING] No existing payment found, proceeding to upload');

            // Upload bukti transfer (sama seperti di Admin ProductController)
            if (!$request->hasFile('bukti_transfer')) {
                \Log::error('[PAYMENT_ERROR] File not found in request');
                return back()->withErrors(['error' => 'File bukti pembayaran tidak ditemukan. Silakan pilih file terlebih dahulu.']);
            }

            $file = $request->file('bukti_transfer');
            \Log::info('[PAYMENT_FILE_RECEIVED] File received', ['size' => $file->getSize(), 'mime' => $file->getMimeType()]);

            // Validasi file
            if (!$file->isValid()) {
                \Log::error('[PAYMENT_ERROR] File is not valid');
                return back()->withErrors(['error' => 'File tidak valid. Silakan pilih file lain.']);
            }

            \Log::info('[PAYMENT_FILE_STORING] Starting file store operation');
            // Store file
            $buktiPath = $file->store('payment_proofs', 'public');
            \Log::info('[PAYMENT_FILE_STORED] File stored', ['path' => $buktiPath]);

            if (!$buktiPath) {
                \Log::error('[PAYMENT_ERROR] File store returned null');
                return back()->withErrors(['error' => 'Gagal mengupload file. Pastikan folder storage memiliki permission yang benar.']);
            }

            \Log::info('[PAYMENT_CREATING] Creating payment record', ['order_id' => $order->id_order, 'amount' => $order->total_harga]);
            // CREATE payment record dengan bukti transfer
            $payment = Payment::create([
                'id_order' => $order->id_order,
                'metode_pembayaran' => 'qris',
                'jumlah' => $order->total_harga,
                'status' => 'pending',
                'bukti_transfer' => $buktiPath,
                'keterangan' => $validated['trx_id'] ? "Ref: {$validated['trx_id']}" : null
            ]);

            \Log::info('[PAYMENT_SUCCESS] Payment created successfully', [
                'payment_id' => $payment->id_payment,
                'order_id' => $order->id_order,
                'bukti_path' => $buktiPath,
                'file_exists' => $buktiPath ? \Storage::disk('public')->exists($buktiPath) : false,
            ]);

            // Clear session order_id setelah payment berhasil
            session()->forget(['_order_id', 'last_order_id']);

            // Redirect ke halaman orders dengan notifikasi
            return redirect()->route('orders.my')->with([
                'success' => 'Bukti pembayaran berhasil diupload! Sedang diverifikasi oleh admin.',
                'notification' => [
                    'type' => 'success',
                    'title' => 'Pembayaran Berhasil Diupload!',
                    'message' => 'Bukti pembayaran Anda sedang diverifikasi oleh admin.',
                    'order_id' => $order->id_order,
                    'payment_id' => $payment->id_payment
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('[PAYMENT_EXCEPTION] Payment confirmation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => session()->getId(),
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
