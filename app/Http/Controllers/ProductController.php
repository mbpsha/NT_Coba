<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    // Public API methods
    public function index()
    {
        return response()->json(Product::all());
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // Admin methods
    public function indexAdmin()
    {
        $products = Product::latest()->paginate(10);

        return Inertia::render('ProductsManagement', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return Inertia::render('ProductsManagement');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategori' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
        ]);

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $imagePath = $request->file('gambar')->store('products', 'public');
            $validated['gambar'] = '/storage/' . $imagePath;
        }

        Product::create($validated);

        return redirect()->back()->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return Inertia::render('ProductsManagement', [
            'product' => $product
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kategori' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
        ]);

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($product->gambar) {
                $oldPath = str_replace('/storage/', '', $product->gambar);
                Storage::disk('public')->delete($oldPath);
            }

            $imagePath = $request->file('gambar')->store('products', 'public');
            $validated['gambar'] = '/storage/' . $imagePath;
        } else {
            unset($validated['gambar']);
        }

        $product->update($validated);

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete image if exists
        if ($product->gambar) {
            $imagePath = str_replace('/storage/', '', $product->gambar);
            Storage::disk('public')->delete($imagePath);
        }

        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully!');
    }
}
