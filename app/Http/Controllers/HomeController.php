<?php

namespace App\Http\Controllers;

use App\Services\CacheService;

class HomeController extends Controller
{
    /**
     * Display the homepage with published posts.
     *
     * URL: / (homepage)
     * Fetches published posts only
     * Loads relationship (user, category, tags) to avoid extra queries
     * Orders by newest published_at.
     * Paginates results (10 per page).
     */
    public function index()
    {
        // Using a caching service to optimize performance, especially for high traffic.
        $posts = CacheService::getPublishedPosts(
            page: request('page', 1),
            perPage: 10
            // This is PHP 8.1 named arguments feature
        );

        return view('welcome', compact('posts'));

    }
}
