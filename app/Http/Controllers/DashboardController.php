<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        $today = Carbon::today();

        return response()->json([
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'today_revenue' => Order::whereDate('created_at', $today)
                                   ->where('status', '!=', 'dibatalkan')
                                   ->sum('total_harga'),
            'pending_payments' => Payment::where('status_pembayaran', 'pending')->count(),
            'low_stock_products' => Product::where('stok', '<', 10)->count()
        ]);
    }

    public function getTodayTransactions()
    {
        $today = Carbon::today();

        $transactions = Order::with(['user', 'payment'])
                           ->whereDate('created_at', $today)
                           ->orderBy('created_at', 'desc')
                           ->get();

        return response()->json($transactions);
    }

    public function getSalesData(Request $request)
    {
        $days = $request->input('days', 7); // Default 7 hari terakhir

        $salesData = Order::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('COUNT(*) as total_orders'),
                            DB::raw('SUM(total_harga) as total_revenue')
                        )
                        ->where('created_at', '>=', Carbon::now()->subDays($days))
                        ->where('status', '!=', 'dibatalkan')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get();

        return response()->json($salesData);
    }

    public function getProductStats()
    {
        $products = Product::select('nama', 'stok')
                          ->orderBy('stok', 'asc')
                          ->limit(10)
                          ->get();

        return response()->json($products);
    }

    public function verifyPayment(Request $request, $paymentId)
    {
        $request->validate([
            'status' => 'required|in:berhasil,gagal',
            'bukti_pembayaran' => 'nullable|string'
        ]);

        $payment = Payment::findOrFail($paymentId);
        $payment->status_pembayaran = $request->status;

        if ($request->bukti_pembayaran) {
            $payment->bukti_pembayaran = $request->bukti_pembayaran;
        }

        $payment->save();

        // Update order status jika payment berhasil
        if ($request->status === 'berhasil') {
            $order = Order::find($payment->id_pemesanan);
            $order->status = 'dikonfirmasi';
            $order->save();
        }

        return response()->json(['message' => 'Status pembayaran berhasil diupdate']);
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,dikonfirmasi,diproses,dikirim,selesai,dibatalkan'
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Status order berhasil diupdate']);
    }
}
