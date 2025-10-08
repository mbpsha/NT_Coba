<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use Illuminate\Http\Request;

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
}
