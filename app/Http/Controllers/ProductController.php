<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::orderByDesc('created_at')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'stock' => $p->stock,
                'price' => (float) $p->price,
                'launch_date' => $p->launch_date,
                'eta_date' => $p->eta_date,
                'image_url' => $p->image_path ? (str_starts_with($p->image_path, 'http') ? $p->image_path : asset('storage/'.$p->image_path)) : null,
                'created_at' => $p->created_at,
            ];
        });
        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'launch_date' => 'nullable|date',
            'eta_date' => 'nullable|date',
            'image' => 'nullable|image|max:4096',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'price' => $data['price'],
            'launch_date' => $data['launch_date'] ?? null,
            'eta_date' => $data['eta_date'] ?? null,
            'image_path' => $path,
        ]);

        return response()->json([
            'message' => 'Product created',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'stock' => $product->stock,
                'price' => (float) $product->price,
                'launch_date' => $product->launch_date,
                'eta_date' => $product->eta_date,
                'image_url' => $path ? asset('storage/'.$path) : null,
                'created_at' => $product->created_at,
            ]
        ], 201);
    }
}
