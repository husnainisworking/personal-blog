<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
           'name' => 'required|max:255',
           'email' => 'required|email|max:255',
            'content' => 'required|max:1000'
        ]);

        $validated['post_id'] = $post->id;
        $validated['approved'] = false; //requires admin approval

        Comment::create($validated);

        return back()->with('success', 'Comment submitted! It will appear after approval.');
        //validation ensures the visitor provides a name, valid email, and comment
        //content (<1000 chars).
        //Link to post: Adds the post_id so the comment is tied to the correct post.
        // approval flag: sets approved = false so the comment won't show publicly until
        // an admin approves it.
        // save: creates the comment in the database.
        // redirect: sends the user back with a success message.
        // this is the form visitors use to leave comments under a post.

    }
    // show all comments (Admin)
    public function index()
    {
        $comments = Comment::with('post')
            ->latest()
            ->paginate(20);

        return view ('comments.index', compact('comments'));
        // with('post') -> Loads the related post for each comment (so you know
        // which post it belongs to
        /** latest() -> order comments by newest first.
         * paginate(20) ->shows 20 comments per page.
         * view: passes comments to comments/index.blade.php
         * This is the admin dashboard page listing all the comments.
         */
    }
    // Approve comment
    public function approve(Comment $comment)
    {
        $comment->update(['approved' => true]);

        return back()->with('success' , 'Comment approved!');

        // updates the comment's approved field to true.
        // redirects back with a success message.
        // this is how an admin makes a comment visible on the public site.
    }
    // delete comment
    public function destroy (Comment $comment)
    {
        $comment->delete();
        // delete the comment from the database
        return back()->with('success', 'Comment deleted!');
        // redirect back with a success message.
        // this is how an admin removes unwanted comments.
    }



























}
