<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Show all categories
     */
    public function index()
    {
        //withCount() is a Laravel Eloquent builder method that adds
        // posts_count column to each Category model.
        // get() = executes the query and returns a collection of Category objects.
        // this is the page that lists all categories with their post counts.
        $categories = Category::withCount('posts')->get();
        return view('categories.index', compact ('categories'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        //simply loads the Blade view categories/create.blade.php
        // this is the form where you enter a new category name and description.
        return view('categories.create');
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
           'name' => 'required|unique:categories|max:255',
            'description' => 'nullable|max:1000'
        ]);
        $validated['slug'] = Str::slug($validated['name']);

        //now going to insert new category into the database.
        Category::create($validated);
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
        //passes the specific category you want to edit.
    }

    /**
     * Update category. (Update an existing category)
     */
    public function update(Request $request,  Category $category)
    {
        $validated = $request->validate([
           'name' => 'required|max: 255|unique:categories,name,'. $category->id,
            //name must exist, < 255 chars, and unique (except for the current category being updated).
            'description' => 'nullable|max:1000'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');

    }

    /**
     * Delete a category
     */
    public function destroy( Category $category)
    {
        $category->delete();
        //deletes the category from the database.
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
        //redirect back to the category list with a success message.
    }

    /**
     * Show posts by category (Public)
     */
    public function show(Category $category)
    {
        $posts = $category->posts() //get all posts linked to this category
            ->published() //use a local scope to only fetch posts with status = published
            ->with(['user', 'tags'])  //eager loads relationships (author and tags) to avoid N+1 queries.
            ->latest('published_at') //orders posts by newest published date
            ->paginate(10); //splits results into pages of 10 posts each.

        return view ('categories.show', compact('category', 'posts'));
    }
}
























