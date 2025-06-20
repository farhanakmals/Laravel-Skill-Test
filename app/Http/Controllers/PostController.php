<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')
            ->where('is_draft', false)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->latest()
            ->paginate(20);

        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_draft' => 'required|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = Auth::user()->posts()->create($validated);

        return response()->json([
            'message' => 'Post created successfully.',
            'data' => $post,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if ($post->is_draft || ($post->published_at && $post->published_at->isFuture())) {
            return response()->json(['message' => 'Post not found.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'is_draft' => 'sometimes|required|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post updated successfully.',
            'data' => $post,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_FORBIDDEN);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }
}
