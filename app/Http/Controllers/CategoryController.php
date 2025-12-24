<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Show all categories
     */
    public function index(): View
    {
        $this->authorize('viewAny', Category::class);

        // withCount() is a Laravel Eloquent builder method that adds
        // posts_count column to each Category model.
        // get() = executes the query and returns a collection of Category objects.
        // this is the page that lists all categories with their post counts.
        $categories = Category::withCount('posts')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);

        // simply loads the Blade view categories/create.blade.php
        // this is the form where you enter a new category name and description.
        return view('categories.create');
    }

    /**
     * Store new category
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Use atomic slug generation
        $slug = SlugService::generateWithRetry(
            $validated['name'],
            Category::class,
            null,
            function ($generatedSlug) use (&$validated) {
                $validated['slug'] = $generatedSlug;

                DB::transaction(function () use ($validated) {
                    Category::create($validated);
                });
            });

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', compact('category'));
        // passes the specific category you want to edit.
    }

    /**
     * Update category. (Update an existing category)
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        // Use atomic slug generation for updates
        $validated['slug'] = SlugService::updateSlug(

            $category,
            $validated['name'],
            Category::class
        );

        DB::transaction(function () use ($category, $validated) {
            $category->update($validated);
        });

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete a category
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $category->delete();

        // deletes the category from the database.
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
        // redirect back to the category list with a success message.
    }

    /**
     * Show posts by category (Public)
     */
    public function show(Category $category): View
    {
        $perPage = config('pagination.categories');

        $posts = Post::published() // get all posts linked to this category
            ->where('category_id', $category->id)
            ->with(['user', 'tags'])  // eager loads relationships (author and tags) to avoid N+1 queries.
            ->latest('published_at') // orders posts by newest published date
            ->paginate($perPage); // splits results into pages of 10 posts each.

        /** @phpstan-ignore-next-line */
        return view('categories.show', compact('category', 'posts'));
    }
}
