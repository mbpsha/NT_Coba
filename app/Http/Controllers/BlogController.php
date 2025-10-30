<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Http\Requests\BlogRequest;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return response()->json(Blog::all());
    }

    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);
    }

    public function store(BlogRequest $request)
    {
        $blog = Blog::create($request->validated());
        return response()->json($blog, 201);
    }

    public function update(BlogRequest $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->update($request->validated());
        return response()->json($blog);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();
        return response()->json(null, 204);
    }
    public function blog()
    {
        return Inertia::render('User/Blog');
    }
}
