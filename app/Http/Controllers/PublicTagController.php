<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\View\View;

class PublicTagController extends Controller
{
    public function index(): View
    {
        // Count only published posts per tag
        $tags = Tag::query()
            ->withCount(['posts as posts_count' => function ($q) {
                $q->published();
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('tags.public-index', compact('tags'));

    }
}
