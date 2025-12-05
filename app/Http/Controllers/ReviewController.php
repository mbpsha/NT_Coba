<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\OrderDetail;
use App\Http\Requests\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(Review::all());
    }

    public function show($id)
    {
        $review = Review::findOrFail($id);
        return response()->json($review);
    }

    public function store(ReviewRequest $request)
    {
        $review = Review::create($request->validated());
        return response()->json($review, 201);
    }

    public function update(ReviewRequest $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update($request->validated());
        return response()->json($review);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json(null, 204);
    }

    /**
     * Halaman penilaian untuk user (Inertia).
     */
    public function ratingPage(Request $request)
    {
        $user = $request->user();

        $eligibleStatuses   = ['selesai', 'delivered'];
        $reviewedProductIds = Review::where('id_user', $user->id_user)->pluck('id_produk')->all();

        $pendingDetails = OrderDetail::with(['product', 'order'])
            ->whereHas('order', function ($query) use ($user, $eligibleStatuses) {
                $query->where('id_user', $user->id_user)
                    ->whereIn('status', $eligibleStatuses);
            })
            ->when(!empty($reviewedProductIds), function ($query) use ($reviewedProductIds) {
                $query->whereNotIn('id_produk', $reviewedProductIds);
            })
            ->latest()
            ->get();

        $pending = $pendingDetails->map(function ($detail) {
            $product = $detail->product;
            $order   = $detail->order;

            $image = $product?->gambar ?: '/assets/dashboard/profil.png';

            return [
                'detail_id'   => $detail->id_order_detail,
                'id_produk'   => $detail->id_produk,
                'name'        => $product?->nama_produk ?? 'Produk tidak tersedia',
                'note'        => $product?->deskripsi ? Str::limit(strip_tags($product->deskripsi), 80) : null,
                'date'        => $order?->updated_at?->format('d M Y') ?? $order?->created_at?->format('d M Y'),
                'image'       => $image,
                'status_text' => $order?->status ?? 'selesai',
            ];
        });

        $history = Review::with('product')
            ->where('id_user', $user->id_user)
            ->latest()
            ->get()
            ->map(function ($review) {
                $product = $review->product;
                $image   = $product?->gambar ?: '/assets/dashboard/profil.png';

                return [
                    'id'         => $review->id_review,
                    'id_produk'  => $review->id_produk,
                    'name'       => $product?->nama_produk ?? 'Produk tidak tersedia',
                    'note'       => $product?->deskripsi ? Str::limit(strip_tags($product->deskripsi), 80) : null,
                    'date'       => $review->created_at?->format('d M Y'),
                    'image'      => $image,
                    'rating'     => $review->rating,
                    'reviewText' => $review->komentar,
                ];
            });

        return Inertia::render('User/ReviewsAndHistory', [
            'pendingReviews' => $pending,
            'history'        => $history,
        ]);
    }

    /**
     * Simpan penilaian dari user setelah pesanan selesai.
     */
    public function submitFromUser(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'id_produk' => 'required|exists:products,id_produk',
            'rating'    => 'required|integer|min:1|max:5',
            'komentar'  => 'nullable|string|max:1000',
        ]);

        $eligibleStatuses = ['selesai', 'delivered'];

        $eligible = OrderDetail::where('id_produk', $validated['id_produk'])
            ->whereHas('order', function ($query) use ($user, $eligibleStatuses) {
                $query->where('id_user', $user->id_user)
                    ->whereIn('status', $eligibleStatuses);
            })
            ->exists();

        if (!$eligible) {
            return back()
                ->withErrors(['rating' => 'Produk ini belum bisa dinilai. Pastikan pesanan sudah berstatus selesai.'])
                ->withInput();
        }

        Review::updateOrCreate(
            [
                'id_produk' => $validated['id_produk'],
                'id_user'   => $user->id_user,
            ],
            [
                'rating'   => $validated['rating'],
                'komentar' => $validated['komentar'] ?? null,
            ]
        );

        return back()->with('success', 'Terima kasih! Penilaianmu berhasil disimpan.');
    }
}
