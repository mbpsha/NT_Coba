<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'totalProducts' => Product::count(),
            'totalUsers' => User::where('role', '!=', 'admin')->count(),
            'totalOrders' => Order::count(),
            'pendingPayments' => Payment::where('status', 'pending')->count(),
        ];

        // Recent products (last 5)
        $recentProducts = Product::latest()
            ->take(5)
            ->get();

        // Recent users (last 5)
        $recentUsers = User::latest()
            ->take(5)
            ->get();

        // Monthly sales for chart (last 12 months)
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $sales = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'selesai')
                ->sum('total_harga');

            $monthlySales[] = [
                'month' => $month->format('M'),
                'amount' => $sales ?? 0
            ];
        }

        return Inertia::render('Admin/DashboardAdmin', [
            'stats' => $stats,
            'recentProducts' => $recentProducts,
            'recentUsers' => $recentUsers,
            'monthlySales' => $monthlySales,
        ]);
    }
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
