<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\CartRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return response()->json(Cart::all());
    }

    public function show($id)
    {
        $cart = Cart::findOrFail($id);
        return response()->json($cart);
    }

    public function store(CartRequest $request)
    {
        $cart = Cart::create($request->validated());
        return response()->json($cart, 201);
    }

    public function update(CartRequest $request, $id)
    {
        $cart = Cart::findOrFail($id);
        $cart->update($request->validated());
        return response()->json($cart);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();
        return response()->json(null, 204);
    }
}
