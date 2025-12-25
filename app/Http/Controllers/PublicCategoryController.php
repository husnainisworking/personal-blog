<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class PublicCategoryController extends Controller
{
    public function index(): View
    {
        // Count only published posts per category
        $categories = Category::query()
            ->withCount(['posts as post_count' => function ($q) {
                $q->published();
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);

        return view('categories.public-index', compact('categories'));
    }
}
