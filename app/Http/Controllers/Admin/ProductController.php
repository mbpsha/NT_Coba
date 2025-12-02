<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(20);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'props' => [
                    'products' => $products
                ]
            ]);
        }

        return inertia('Admin/Products/Index', [
            'products' => $products
        ]);
    }

    public function create(Request $request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'props' => [
                    'form' => 'create'
                ]
            ]);
        }

        return inertia('Admin/Products/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'kategori' => 'nullable|string',
        ]);

        $product = Product::create($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Product created successfully!',
                'product' => $product
            ], 201);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'gambar' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'kategori' => 'nullable|string',
        ]);

        $product->update($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Product updated successfully!',
                'product' => $product->fresh()
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy(Request $request, Product $product)
    {
        $product->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Product deleted successfully!'
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}
