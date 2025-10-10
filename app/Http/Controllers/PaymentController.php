<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return response()->json(Payment::all());
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return response()->json($payment);
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
     * Upload payment proof (bukti pembayaran)
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120' // Max 5MB
        ]);

        $payment = Payment::findOrFail($id);

        // Upload file
        $file = $request->file('bukti_pembayaran');
        $filename = 'payment_proof_' . $payment->id_pembayaran . '_' . time() . '.' . $file->extension();
        $path = $file->storeAs('payment_proofs', $filename, 'public');

        // Update payment record
        $payment->update([
            'bukti_pembayaran' => $path,
            'status_pembayaran' => 'pending', // Wait for admin verification
            'payment_date' => now()
        ]);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diupload',
            'payment' => $payment,
            'file_url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Get QR Code and payment details for checkout
     */
    public function getQRCode($id)
    {
        $payment = Payment::with('order')->findOrFail($id);

        // Generate QR code data untuk QRIS
        $qrData = [
            'merchant' => 'NGUNDUR',
            'amount' => $payment->jumlah,
            'transaction_id' => $payment->id_pembayaran,
            'payment_method' => 'QRIS'
        ];

        return response()->json([
            'payment_details' => [
                'id_pembayaran' => $payment->id_pembayaran,
                'metode_pembayaran' => $payment->metode_pembayaran,
                'jumlah' => $payment->jumlah,
                'status' => $payment->status_pembayaran,
                'tanggal' => $payment->created_at->format('d M Y'),
                'payment_method' => 'QRIS'
            ],
            'qr_code_data' => base64_encode(json_encode($qrData)),
            'merchant_info' => [
                'name' => 'NGUNDUR',
                'logo' => asset('images/logo.png')
            ]
        ]);
    }

    /**
     * Get payment status for tracking
     */
    public function getPaymentStatus($id)
    {
        $payment = Payment::findOrFail($id);

        return response()->json([
            'id_pembayaran' => $payment->id_pembayaran,
            'status' => $payment->status_pembayaran,
            'jumlah' => $payment->jumlah,
            'metode_pembayaran' => $payment->metode_pembayaran,
            'bukti_pembayaran' => $payment->bukti_pembayaran ? asset('storage/' . $payment->bukti_pembayaran) : null,
            'payment_date' => $payment->payment_date,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at
        ]);
    }
}
