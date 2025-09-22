<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    // ambil semua produk
    public function index()
    {
        return response()->json(Product::all());
    }
}
