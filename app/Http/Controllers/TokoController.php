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
            ->paginate(6)
            ->through(fn ($p) => [
                'id_produk'   => $p->id_produk,
                'nama_produk' => $p->nama_produk,
                'harga'       => (int) $p->harga,
                'gambar'      => $p->gambar,
            ]);

        $orderStatus = Auth::check()
            ? (Order::where('id_user', Auth::id())->latest('created_at')->value('status') ?? 'paid')
            : 'paid';

        return Inertia::render('User/Toko', [
            'products'    => $products,
            'orderStatus' => $orderStatus,
        ]);
    }
}
