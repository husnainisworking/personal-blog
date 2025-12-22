<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle search requests for blog posts.
     *
     * Searches posts by title and content
     * user types "Laravel" in blog's search box.
     * browser hits /search?q=Laravel
     */
    public function index(Request $request)
    {
        $query = $request->input('q');

        $perPage = config('pagination.search');

        $posts = Post::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                // %{$query}% means "anywhere in the string"
                // search posts by title/content
                    ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['user', 'category', 'tags'])
            ->latest('published_at')
            ->paginate($perPage);

        return view('search', compact('posts', 'query'));
    }
}
