<?php

namespace App\Http\Controllers;

use App\Models\CartDetail;
use App\Models\Product;
use App\Http\Requests\CartDetailRequest;
use Illuminate\Http\Request;

class CartDetailController extends Controller
{
    public function index()
    {
        return response()->json(CartDetail::with(['cart', 'product'])->get());
    }

    public function getCartItems($cartId)
    {
        $cartItems = CartDetail::with('product')
                            ->where('id_keranjang', $cartId)
                            ->get();
        return response()->json($cartItems);
    }

    public function store(CartDetailRequest $request)
    {
        // Cek apakah produk sudah ada di keranjang
        $existingItem = CartDetail::where('id_keranjang', $request->id_keranjang)
                                ->where('id_produk', $request->id_produk)
                                ->first();

        if ($existingItem) {
            // Update jumlah jika sudah ada
            $existingItem->jumlah += $request->jumlah;
            $existingItem->save();
            return response()->json($existingItem);
        }

        // Buat item baru jika belum ada
        $cartDetail = CartDetail::create($request->validated());
        return response()->json($cartDetail, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function update(CartDetailRequest $request, $id)
    {
        $cartDetail = CartDetail::findOrFail($id);
        $cartDetail->update($request->validated());
        return response()->json($cartDetail);
    }

    public function destroy($id)
    {
        $cartDetail = CartDetail::findOrFail($id);
        $cartDetail->delete();
        return response()->json(null, 204);
    }

    public function updateQuantity(Request $request, $id)
    {
        // allow zero so frontend can decrement to 0 -> remove item
        $request->validate(['jumlah' => 'required|integer|min:0']);

        $cartDetail = CartDetail::findOrFail($id);

        $newQty = (int) $request->jumlah;

        if ($newQty <= 0) {
            // remove item from cart when quantity becomes zero
            $cartDetail->delete();
            return response()->json(null, 204);
        }

        $cartDetail->jumlah = $newQty;
        $cartDetail->save();

        return response()->json($cartDetail);
    }
}
