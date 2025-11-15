<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;
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
        $payments = Payment::with(['order.user', 'order.orderDetails.product'])
            ->latest()
            ->paginate(10);

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
        $payment = Payment::findOrFail($id);

        return response()->json([
            'id_payment' => $payment->id_payment,
            'status' => $payment->status,
            'jumlah' => $payment->jumlah,
            'metode_pembayaran' => $payment->metode_pembayaran,
            'bukti_transfer' => $payment->bukti_transfer ? asset('storage/' . $payment->bukti_transfer) : null,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at
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
        $validated = $request->validate([
            'trx_id' => 'nullable|string|max:100',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'agree' => 'required|accepted'
        ]);

        try {
            // Ambil order_id dari URL parameter, session, atau flash data
            $orderId = $id_order ?? session('_order_id') ?? session('last_order_id');

            if (!$orderId) {
                return back()->withErrors(['error' => 'Session order tidak ditemukan. Silakan buat pesanan terlebih dahulu.']);
            }

            // Cek apakah order ada
            $order = Order::find($orderId);

            if (!$order) {
                return back()->withErrors(['error' => 'Order tidak ditemukan. Silakan buat pesanan baru.']);
            }

            // Validasi order milik user yang login
            if ($order->id_user !== $request->user()->id_user) {
                return back()->withErrors(['error' => 'Order tidak valid.']);
            }

            // Cek apakah payment sudah ada
            $existingPayment = Payment::where('id_order', $order->id_order)->first();
            if ($existingPayment) {
                return back()->withErrors(['error' => 'Pembayaran untuk pesanan ini sudah pernah diupload. Status: ' . $existingPayment->status]);
            }

            // Upload bukti transfer
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $file = $request->file('bukti_transfer');
                $filename = 'bukti_' . $order->id_order . '_' . time() . '.' . $file->extension();
                $buktiPath = $file->storeAs('payment_proofs', $filename, 'public');
            }

            // CREATE payment record dengan bukti transfer
            $payment = Payment::create([
                'id_order' => $order->id_order,
                'metode_pembayaran' => 'qris',
                'jumlah' => $order->total_harga,
                'status' => 'pending', // Menunggu verifikasi admin
                'bukti_transfer' => $buktiPath ? 'payment_proofs/' . basename($buktiPath) : null,
                'keterangan' => $validated['trx_id'] ? "Ref: {$validated['trx_id']}" : null
            ]);

            // Order status tetep 'pending' - nanti diupdate pas payment verified

            // Clear session order_id setelah payment berhasil
            session()->forget(['_order_id', 'last_order_id']);

            // Return dengan notification data untuk FE handle pop-up
            return back()->with([
                'payment_submitted' => true,
                'notification' => [
                    'type' => 'success',
                    'title' => 'Pembayaran Berhasil Diupload!',
                    'message' => 'Bukti pembayaran Anda sedang diverifikasi oleh admin. Anda dapat melihat status pesanan di halaman Pesanan Saya.',
                    'order_id' => $order->id_order,
                    'payment_id' => $payment->id_payment
                ]
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
