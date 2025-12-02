<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'orderDetails.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'props' => [
                    'orders' => $orders
                ]
            ]);
        }

        return inertia('Admin/Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $order->load(['user', 'address', 'orderDetails.product', 'payment']);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'props' => [
                    'order' => $order,
                    'payment' => $order->payment
                ]
            ]);
        }

        return inertia('Admin/Orders/Show', [
            'order' => $order
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,dikirim,selesai,dibatalkan'
        ]);

        $order->update(['status' => $validated['status']]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Order status updated successfully!',
                'order' => $order->fresh()
            ]);
        }

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
