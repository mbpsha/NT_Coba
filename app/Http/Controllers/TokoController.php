<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class TokoController extends Controller
{
    public function index()
    {
        $products = Product::select('id_produk','nama_produk','harga','gambar')
            ->latest('id_produk')
            ->paginate(9);

        $orderStatus = null;
        if (Auth::check()) {
            $orderStatus = Order::where('id_user', Auth::id())
                ->latest('created_at')
                ->value('status');
        }

        return Inertia::render('User/Toko', [
            'products'    => $products,
            'orderStatus' => $orderStatus,
        ]);
    }
}
