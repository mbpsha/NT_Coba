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

        return Inertia::render('DashboardAdmin', [
            'stats' => $stats,
            'recentProducts' => $recentProducts,
            'recentUsers' => $recentUsers,
            'monthlySales' => $monthlySales,
        ]);
    }
}
