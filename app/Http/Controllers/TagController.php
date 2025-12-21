<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Models\Post;
use App\Models\Tag;
use App\Services\SlugService;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * Show all tags
     */
    public function index()
    {
        $this->authorize('viewAny', Tag::class);

        $tags = Tag::withCount('posts')->get();

        // Tag::withCount('posts') -> Fetches all tags and adds a posts_count
        // column showing how many posts are linked to each tag.
        // get() -> Executes the query and returns a collection of Tag objects.
        return view('tags.index', compact('tags'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Tag::class);

        return view('tags.create');
        // loads the Blade view tags/create.blade.php
        // this is the form where you enter a new tag name.
    }

    /**
     * Store new tag
     */
    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        // Use atomic slug generation
        $slug = SlugService::generateWithRetry(
            $validated['name'],
            Tag::class,
            null,
            function ($generatedSlug) use (&$validated) {
                $validated['slug'] = $generatedSlug;

                DB::transaction(function () use (&$validated) {
                    Tag::create($validated);
                });

            }
        );

        return redirect()->route('tags.index')
            ->with('success', 'Tag created successfully!');

    }

    /**
     * Show the edit form
     */
    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);

        return view('tags.edit', compact('tag'));
        // loads the Blade view tags/edit.blade.php
        // passes the specific tag you want to edit.
    }

    /**
     * Update an existing tag
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        // Use atomic slug generation for updates
        $validated['slug'] = SlugService::updateSlug(
            $tag,
            $validated['name'],
            Tag::class
        );

        DB::transaction(function () use ($tag, $validated) {
            $tag->update($validated);
        });

        return redirect()->route('tags.index')
            ->with('success', 'Tag updated successfully!');

    }

    /**
     * Delete a tag
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', 'Tag deleted successfully!');
    }

    /**
     * Show posts by tag (Public)
     */
    public function show(Tag $tag)
    {
        $posts = Post::published()
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->with(['user', 'category'])
            ->latest('published_at')
            ->paginate(10);

        return view('tags.show', compact('tag', 'posts'));
        // $tag->posts() Gets all posts linked to this tag.
        // published() -> uses a local scope to only fetch posts with status = published.
        // with(['user', 'category']) -> eager loads relationships (author and category) to avoid
        // N+1 queries.
        // latest('published_at) ->orders posts by newest published date.
        // paginate(10) ->splits results into pages of 10 pages each.

        // View:
        // Passes both the tag and its posts to tags/show.blade.php
    }
}
