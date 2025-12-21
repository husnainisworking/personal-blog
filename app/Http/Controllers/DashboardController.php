<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;

class DashboardController extends Controller
{
    /**
     *  Display the admin dashboard with statistics.
     *
     *  URL: /dashboard
     *  Collect site statistics:
     *  Total comments, pending comments.
     *  Total categories, total tags.
     *  This is the admin overview page.
     */
    public function index()
    {

        // No authorization check needed - middleware handles it!

        $stats = [

            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'draft_posts' => Post::where('status', 'draft')->count(),
            'total_comments' => Comment::count(),
            'pending_comments' => Comment::where('approved', false)->count(),
            'total_tags' => Tag::count(),
            'total_categories' => Category::count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
