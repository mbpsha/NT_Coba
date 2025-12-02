<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function verify(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $newStatus = $validated['action'] === 'approve' ? 'verified' : 'rejected';

        $payment->update(['status' => $newStatus]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Payment verification updated successfully!',
                'payment' => $payment->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Verifikasi pembayaran berhasil diperbarui!');
    }
}
