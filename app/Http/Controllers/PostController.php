<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\SlugService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Shows all posts (Admin)
     */
    public function index()
    {
        $this->authorize('viewAny', Post::class);

        // This method is a controller action that handles showing all posts
        // in your admin panel. index() is conventional name for "list all items".
        // Eager loading occurs here.

        $posts = Post::with(['user', 'category', 'tags'])
            ->latest() // orders posts by the newest first (usually by created_at)
            ->paginate(10); // splits results into pages of 10 posts each, laravel automatically handles page links (?page=2, etc.).

        return view('posts.index', compact('posts'));
    }

    /**
     * Show trashed posts (soft deleted)
     */
    public function trashed()
    {
        $this->authorize('viewAny', Post::class);
        // This method shows all soft-deleted posts in the admin panel.

        $posts = Post::onlyTrashed()
            ->with(['user', 'category', 'tags'])
            ->latest()
            ->paginate(10);

        /** @phpstan-ignore-next-line */
        return view('posts.trashed', compact('posts'));
    }

    /**
     * Show create form
     */
    public function create()
    {

        // NEW: Check permission
        $this->authorize('create', Post::class);

        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.create', compact('categories', 'tags'));
    }

    /**
     * Store new post
     */
    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        // If validation fails -> Laravel automatically redirects back with errors.
        // Add the user who created the post, get the currently logged-in user's ID.
        $validated['user_id'] = Auth::id();
        // Generate a slug from the title, turns title into a URL-friendly string.

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image'));
        }

        // Set published date if status is published
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        // Use atomic slug generation with database transaction
        $post = null;
        $slug = SlugService::generateWithRetry(
            $validated['title'],
            Post::class,
            null,
            function ($generatedSlug) use (&$validated, $request) {
                $validated['slug'] = $generatedSlug;

                // Create post within the transaction
                DB::transaction(function () use (&$validated, $request, &$post) {
                    $post = Post::create($validated);

                    // Attach tags if provided
                    if ($request->has('tags')) {
                        $post->tags()->attach($request->tags);
                    }
                });
            }
        );

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully !');
    }

    /**
     * Public view of a single post
     */
    public function show(Post $post)
    {   // Post $post -> laravel automatically injects the post you want to show
        // (based on the route parameter, e.g. /posts/5).
        $post->load(['user', 'category', 'tags', 'approvedComments']);

        // eager loading
        return view('posts.show', compact('post'));
    }

    /**
     * Admin edit form
     */
    public function edit(Post $post)
    {
        // Authorization check
        $this->authorize('update', $post);

        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update post
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // Authorization is handled in StorePostRequest
        // Validation is handled in StorePostRequest

        $validated = $request->validated();

        // Handle image removal
        if ($request->boolean('remove_featured_image')) {
            $this->deleteImage($post->featured_image);
            $validated['featured_image'] = null;
        }

        // Handle new image upload
        elseif ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image) {
                $this->deleteImage($post->featured_image);
            }
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image'));
        }

        // Use atomic slug generation for updates
        $validated['slug'] = SlugService::updateSlug(
            $post,
            $validated['title'],
            Post::class
        );

        // Set published date if status changed to published
        if ($validated['status'] === 'published' && $post->status !== 'published') {
            $validated['published_at'] = now();
        }

        // Update within transaction
        DB::transaction(function () use ($post, $validated, $request) {
            $post->update($validated);

            // Sync tags
            if ($request->has('tags')) {
                $post->tags()->sync($request->tags);
            } else {
                $post->tags()->detach();
            }

        });

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully !');

    }

    /**
     * Soft delete post (move to trash)
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully !');
    }

    /**
     * Restore soft-deleted post
     */
    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id); // find the soft-deleted post by ID, findOrFail throws 404 if not found.

        $this->authorize('restore', $post); // it checks if the user has permission to restore this post.

        $post->restore(); // calls Eloquent's restore() method to un-delete the post.

        return redirect()->route('posts.index')
            ->with('success', 'Post restored successfully !');

    }

    /**
     * Permanently delete a soft-deleted post
     */
    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        /**
         * onlyTrashed() retrieves only soft-deleted posts, $id is the post's ID.
         */
        $this->authorize('forceDelete', $post);

        // Delete image file before force deleting post
        if ($post->featured_image) {
            $this->deleteImage($post->featured_image);
        }

        $post->forceDelete();

        return redirect()->route('posts.trashed')
            ->with('success', 'Post permanently deleted !');
    }

    /**
     * Upload and store featured image
     */
    private function uploadImage($image): string
    {
        // Generate unique filename with optional extension.
        $filename = time().'-'.uniqid().'.'.$image->getClientOriginalExtension();

        // Store in storage/app/public/posts directory
        $path = $image->storeAs('posts', $filename, 'public');

        return $path;
    }

    /**
     * Delete featured image from storage
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
