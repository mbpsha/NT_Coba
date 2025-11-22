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
        $user = Auth::user();
        $cart = Cart::where('id_user', $user->id_user)->first();

        $items = [];
        if ($cart) {
            $items = CartDetail::where('id_keranjang', $cart->id_keranjang)
                ->with('product')
                ->get()
                ->map(function ($row) {
                    return [
                        'id_detail_keranjang' => $row->id_detail_keranjang,
                        'id_produk'           => $row->id_produk,
                        'qty'                 => $row->jumlah,
                        'product'             => [
                            'id_produk'   => $row->product->id_produk,
                            'nama_produk' => $row->product->nama_produk,
                            'deskripsi'   => $row->product->deskripsi,
                            'harga'       => (int)$row->product->harga,
                            'gambar'      => $row->product->gambar,
                        ],
                    ];
                });
        }

        $subtotal = collect($items)->reduce(fn($s,$it)=> $s + ($it['product']['harga'] * $it['qty']), 0);
        $admin    = 5000;    // sesuaikan kalau ada aturan lain
        $ongkir   = 20000;   // sesuaikan kalau dinamis
        $total    = $subtotal + $admin + $ongkir;

        return Inertia::render('User/Cart', [
            'user'     => [
                'id_user' => $user->id_user,
                'nama'    => $user->nama ?? $user->username ?? '',
            ],
            'items'    => $items,
            'shipping' => ['name'=>'','text'=>'','phone'=>'','is_temp'=>false],
            'summary'  => [
                'admin'    => $admin,
                'ongkir'   => $ongkir,
                'subtotal' => $subtotal,
                'total'    => $total,
            ],
            'product'  => null,
            'qty'      => 1,
        ]);
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