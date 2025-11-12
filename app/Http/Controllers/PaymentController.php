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
     * TESTING MODE: Kalau order belum ada, auto create order dulu
     */
    public function confirmPayment(Request $request, $id_order)
    {
        $validated = $request->validate([
            'trx_id' => 'nullable|string|max:100',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'agree' => 'required|accepted'
        ]);

        try {
            // Cek apakah order ada, kalau belum ada (untuk testing) create dulu
            $order = Order::find($id_order);
            
            if (!$order) {
                // TESTING MODE: Auto create order jika belum ada
                // Untuk production, uncomment validasi di frontend
                $user = $request->user();
                $defaultAddress = \App\Models\Address::where('id_user', $user->id_user)
                    ->where('is_default', true)
                    ->first();
                
                if (!$defaultAddress) {
                    $defaultAddress = \App\Models\Address::where('id_user', $user->id_user)->first();
                }
                
                if (!$defaultAddress) {
                    return back()->withErrors(['error' => 'Alamat belum dilengkapi.']);
                }
                
                $order = Order::create([
                    'id_user' => $user->id_user,
                    'id_alamat' => $defaultAddress->id_alamat,
                    'total_harga' => 10025000, // dummy untuk testing
                    'status' => 'pending'
                ]);
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
                'bukti_transfer' => $buktiPath ? '/storage/' . $buktiPath : null,
                'keterangan' => $validated['trx_id'] ? "Ref: {$validated['trx_id']}" : null
            ]);

            return back()->with('payment_submitted', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
