<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['cartDetails.product'])
        ->where('id_user', Auth::id())
        ->first();

        $items = $cart ? $cart->cartDetails->map(fn($d) => [
                'id_detail_keranjang' => $d->id_detail_keranjang,
                'id_produk'  => $d->product->id_produk, // Langsung di level item untuk kemudahan
                'qty'        => (int)$d->jumlah,
                'added_at'   => optional($d->created_at)->format('d M Y'),
                'avg_rating' => 0,
                'product'    => [
                    'id_produk'   => $d->product->id_produk,
                    'nama_produk' => $d->product->nama_produk,
                    'deskripsi'   => $d->product->deskripsi,
                    'harga'       => (int)$d->harga_satuan,
                    'gambar'      => $d->product->gambar,
                ],
            ])->values() : collect([]);

        return Inertia::render('User/Cart', ['items' => $items]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'id_produk' => 'required|integer|exists:products,id_produk',
            'qty'       => 'nullable|integer|min:1'
        ]);

        $qty = $data['qty'] ?? 1;
        $product = Product::where('id_produk', $data['id_produk'])->firstOrFail();

        $cart = Cart::firstOrCreate(['id_user' => Auth::id()]);
        $detail = CartDetail::where('id_keranjang', $cart->id_keranjang)
            ->where('id_produk', $product->id_produk)
            ->first();

        if ($detail) {
            $detail->increment('jumlah', $qty);
        } else {
            CartDetail::create([
                'id_keranjang' => $cart->id_keranjang,
                'id_produk'    => $product->id_produk,
                'jumlah'       => $qty,
                'harga_satuan' => $product->harga,
            ]);
        }

        return back()->with('cart_added', "Produk berhasil ditambahkan ke keranjang!");
    }

    public function update(Request $request, $id_detail_keranjang)
    {
        $request->validate(['qty' => 'required|integer|min:1']);
        $detail = CartDetail::findOrFail($id_detail_keranjang);
        abort_unless($detail->cart->id_user === Auth::id(), 403);
        $detail->update(['jumlah' => $request->qty]);
        return back();
    }

    public function remove($id_detail_keranjang)
    {
        $detail = CartDetail::findOrFail($id_detail_keranjang);
        abort_unless($detail->cart->id_user === Auth::id(), 403);
        $detail->delete();
        return back();
    }
}
