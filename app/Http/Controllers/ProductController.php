<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk
     * Dapat difilter berdasarkan nama, kategori, dan harga
     * Mendukung pagination dan sorting
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan nama produk
        if ($request->has('search')) {
            $query->where('nama', 'LIKE', '%' . $request->search . '%');
        }

        // Filter berdasarkan range harga
        if ($request->has('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->has('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // Filter produk yang tersedia (stok > 0)
        if ($request->has('available') && $request->available == 1) {
            $query->available();
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'nama');
        $sortOrder = $request->get('sort_order', 'asc');

        if (in_array($sortBy, ['nama', 'harga', 'stok', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => $products
        ]);
    }

    /**
     * Menampilkan detail produk berdasarkan ID
     * Termasuk relasi dengan reviews
     */
    public function show($id)
    {
        $product = Product::with(['reviews.user'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Hitung rata-rata rating
        $averageRating = $product->reviews->avg('rating');

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil',
            'data' => [
                'product' => $product,
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $product->reviews->count()
            ]
        ]);
    }

    /**
     * Menambahkan produk baru
     * Hanya admin yang dapat menambahkan produk
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle upload foto jika ada
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $filename = time() . '_' . Str::random(10) . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('products', $filename, 'public');
                $data['foto'] = $path;
            }

            $product = Product::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate data produk
     * Hanya admin yang dapat mengupdate produk
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            $data = $request->validated();

            // Handle upload foto baru jika ada
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                    Storage::disk('public')->delete($product->foto);
                }

                $foto = $request->file('foto');
                $filename = time() . '_' . Str::random(10) . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('products', $filename, 'public');
                $data['foto'] = $path;
            }

            $product->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
                'data' => $product->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus produk
     * Hanya admin yang dapat menghapus produk
     * Akan mengecek apakah produk masih digunakan di cart atau order
     */
    public function destroy($id)
    {
        try {
            $product = Product::with(['cartDetails', 'orderDetails'])->find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            // Cek apakah produk masih ada di cart atau order
            if ($product->cartDetails->count() > 0 || $product->orderDetails->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak dapat dihapus karena masih digunakan dalam keranjang atau pesanan'
                ], 400);
            }

            // Hapus foto jika ada
            if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                Storage::disk('public')->delete($product->foto);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate stok produk
     * Biasanya digunakan oleh admin untuk manajemen inventory
     */
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stok' => 'required|integer|min:0',
            'action' => 'required|in:set,add,subtract'
        ]);

        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            $newStock = $request->stok;

            switch ($request->action) {
                case 'set':
                    $product->stok = $newStock;
                    break;
                case 'add':
                    $product->increaseStock($newStock);
                    break;
                case 'subtract':
                    if ($product->stok < $newStock) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok tidak mencukupi'
                        ], 400);
                    }
                    $product->decreaseStock($newStock);
                    break;
            }

            if ($request->action == 'set') {
                $product->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Stok produk berhasil diupdate',
                'data' => $product->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan produk terpopuler berdasarkan jumlah penjualan
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 10);

        $products = Product::withCount('orderDetails')
            ->orderBy('order_details_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Produk terpopuler berhasil diambil',
            'data' => $products
        ]);
    }

    /**
     * Menampilkan produk dengan stok menipis
     * Berguna untuk admin dalam manajemen inventory
     */
    public function lowStock(Request $request)
    {
        $threshold = $request->get('threshold', 10);

        $products = Product::where('stok', '<=', $threshold)
            ->orderBy('stok', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Produk dengan stok menipis',
            'data' => $products
        ]);
    }

    /**
     * Bulk update untuk beberapa produk sekaligus
     * Biasanya untuk update harga massal atau status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'updates' => 'required|array'
        ]);

        try {
            $updated = Product::whereIn('id', $request->product_ids)
                ->update($request->updates);

            return response()->json([
                'success' => true,
                'message' => "$updated produk berhasil diupdate"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan bulk update',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
