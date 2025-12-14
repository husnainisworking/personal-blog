<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    /**
     * Shows all posts (Admin)
     */
    public function index()
    {

        //This method is a controller action that handles showing all posts
        // in your admin panel. index() is conventional name for "list all items".
        // Eager loading occurs here.

            $posts = Post::with(['user', 'category', 'tags'])
                ->latest() //orders posts by the newest first (usually by created_at)
                ->paginate(10); //splits results into pages of 10 posts each, laravel automatically handles page links (?page=2, etc.).

            return view('posts.index', compact('posts'));
        }


    /**
     * Show create form
     */
    public function create()
    {
        // Only users with 'create posts' permission can create
        if(!auth()->user()->can('create posts')) {
            abort(403, 'You do not have permission to create posts.');
        }

        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.create', compact('categories', 'tags'));
    }

    /**
     * Store new post
     */
    public function store(Request $request)
    {

        if(!auth()->user()->can('create posts')) {
            abort(403, 'You do not have permission to create posts.');
        }


        //Validate the request
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id', //optional, but must exist in categories table
            'excerpt' => 'nullable|max:500',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array', // optional, must be an array of IDs
            'tags.*' => 'exists:tags,id' // each tag ID must exist in tags table
        ]);

        //If validation fails -> Laravel automatically redirects back with errors.
        //Add the user who created the post, get the currently logged-in user's ID.
        $validated['user_id'] = Auth::id();
        //Generate a slug from the title, turns title into a URL-friendly string.
        $validated['slug'] = Str::slug($validated['title']);

        //Make unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while(Post::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug. '-' . $count;
            $count++;
        }

        //set publish cate if status is published
        // if the post is marked "published", record the current timestamp.
        if($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        //save the post, insert new post into posts table, $post is now the saved Post object.
        $post = Post::create($validated);

        //attach tags, if tags were selected in the form, link them to the post in the pivot table
        //(post_tag) = pivot table
        // example: Post #5 gets linked to Tag# 2 and Tag# 3
        if($request->has('tags')){
            $post->tags()->attach($request->tags);
        }

        //Redirect with success message
        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully !');
        //sends user back to the posts list page, flashes a success message to show at the top.
    }

    /**
     * Public view of a single post
     */
    public function show(Post $post)
    {   //Post $post -> laravel automatically injects the post you want to show
        //(based on the route parameter, e.g. /posts/5).
        $post->load(['user', 'category', 'tags', 'approvedComments']);
        //eager loading
        return view('posts.show', compact('post'));
    }

    /**
     * Admin edit form
     */
    public function edit(Post $post)
    {
        if(!auth()->user()->can('edit posts')) {
            abort(403, 'You do not have permission to edit posts.');
        }

        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update post
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'excerpt' => 'nullable|max:500',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        //update slug if title changed
        $validated['slug'] = Str::slug($validated['title']);

        //make slug unique
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Post::where('slug', $validated['slug'])
            ->where('id', '!=', $post->id)
            ->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }
        //checks if another post already uses the same slug.
        // if yes, appends -1, -2, etc., until it finds a unique slug.
        // the where('id', '!=' , $post->id) ensures it does not conflict with itself.

        //set published date if status changed
        if ($validated['status'] === 'published' && $post->status !== 'published') {
            $validated['published_at'] = now();
        }
        //if the post was previously a draft but is now being published -> set the
        // current timestamp.

        //update the post
        $post->update($validated);
        //saves all new validated data (title, content, slug, status , etc., ) into the database.

        //sync tags
        //if tags were submitted ->sync() updates the pivot table so the post
        //has exactly those tags(add new ones, remove missing ones).
        //if no tags were submitted->detach() removes all tag links for this post.
        //this is going to be many-to-many laravel relationship handling
        //sync([...]) updates the pivot table (post_tag) so that only the given IDs are attached to this post.

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach();
        }

        // redirect with success message , sends the user back to the posts list page.
        // flashes a success message.
        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully !');
    }

        //delete post
        //when we hit a route like /posts/5/delete, Laravel automatically finds the Post with
        // ID = 5 and injects it into $post.
        public function destroy(Post $post)
        {
            if (!auth()->user()->can('delete posts')) {
                abort(403, 'You do not have permission to delete posts.');
            }



            //calls Eloquent's delete() method.
            //this removes the record from the posts table in your database.
            // if you have relationships with onDelete('cascade'), related records
            // (like comments) many also be removed.
            $post->delete(); //this method is from base Model

            return redirect()->route('posts.index')
                ->with('success', 'Post deleted successfully !');

        }



}
