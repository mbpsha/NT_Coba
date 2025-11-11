<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
        $filename = 'payment_proof_' . $payment->id_payment . '_' . time() . '.' . $file->extension();
        $path = $file->storeAs('payment_proofs', $filename, 'public');

        // Update payment record
        $payment->update([
            'bukti_transfer' => '/storage/' . $path,
            'status' => 'pending', // Wait for admin verification
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
            'transaction_id' => $payment->id_payment,
            'payment_method' => 'QRIS'
        ];

        return response()->json([
            'payment_details' => [
                'id_payment' => $payment->id_payment,
                'metode_pembayaran' => $payment->metode_pembayaran,
                'jumlah' => $payment->jumlah,
                'status' => $payment->status,
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
            'id_payment' => $payment->id_payment,
            'status' => $payment->status,
            'jumlah' => $payment->jumlah,
            'metode_pembayaran' => $payment->metode_pembayaran,
            'bukti_transfer' => $payment->bukti_transfer ? asset('storage/' . $payment->bukti_transfer) : null,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at
        ]);
    }

    // Konfirmasi dari Checkout (buat order + payment)
    public function confirmFromCheckout(Request $request, $id_produk)
    {
        $request->validate([
            'qty'            => ['required','integer','min:1'],
            'trx_id'         => ['nullable','string','max:100'],
            'bukti_transfer' => ['required','image','mimes:jpg,jpeg,png','max:5120'],
            'agree'          => ['accepted'],
        ]);

        $qty     = (int) $request->qty;
        $product = Product::where('id_produk', $id_produk)->firstOrFail();
        $user    = Auth::user();

        // Alamat sementara bila ada
        $temp = session('checkout.address');
        $addressText = $temp && (int)($temp['product'] ?? 0) === (int)$id_produk
            ? trim(($temp['nama'] ?? '')."\n".($temp['prov_kab'] ?? '')."\n".($temp['street'] ?? '')."\n".($temp['detail'] ?? ''))
            : (string) ($user->alamat ?? '');

        $hargaProduk = (int) $product->harga;
        $biayaAdmin  = 5000;
        $biayaOngkir = 20000;
        $subtotal    = $hargaProduk * $qty;
        $total       = $subtotal + $biayaAdmin + $biayaOngkir;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'id_user'      => $user->id_user ?? $user->id ?? Auth::id(),
                'total_harga'  => $total,
                'status'       => 'diproses', // terlihat di OrdersManagement
                'address_text' => $addressText,
                'metode'       => 'QRIS',
            ]);

            OrderDetail::create([
                'id_order'  => $order->id_order,
                'id_produk' => $product->id_produk,
                'jumlah'    => $qty,
                'harga'     => $hargaProduk,
            ]);

            $path = $request->file('bukti_transfer')->store('payments', 'public');
            $url  = Storage::url($path);

            Payment::create([
                'id_order'          => $order->id_order,
                'metode_pembayaran' => 'QRIS',
                'jumlah'            => $total,
                'status'            => 'pending', // diverifikasi di PaymentVerification
                'bukti_transfer'    => $url,
                'trx_id'            => $request->trx_id,
            ]);

            if ($temp && (int)($temp['product'] ?? 0) === (int)$id_produk) {
                session()->forget('checkout.address');
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['bukti_transfer' => 'Gagal menyimpan pembayaran.'])->withInput();
        }

        // Redirect ke toko + popup
        return redirect()
            ->route('toko')
            ->with('toast', 'Pemesanan dan Pembayaranmu Sedang Diverivikasi');
    }
}
