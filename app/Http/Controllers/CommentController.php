<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\RateLimiter;
use Mews\Purifier\Facades\Purifier;
use App\Http\Requests\Comment\StoreCommentRequest;




class CommentController extends Controller
{

    /**
     * Store a new comment with professional security measures
     *
     * Security Features:
     * - XSS Protection via HTMLPurifier
     * - Rate Limiting ( 3 per 5 minutes )
     * - Input Validation
     * - Spam Detection
     * - Duplicate Prevention
     */

    public function store(StoreCommentRequest $request, Post $post)
    {
        // Rate Limiting: Prevent spam and abuse
        $rateLimitKey = 'comment-submission:' . $request->ip();

        if (RateLimiter::toomanyAttempts($rateLimitKey, 3) ) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return back()->withErrors([
                'rate limit' => "Too many comment attempts. Please try again in {$seconds} seconds."
            ])->withInput();
        }

        $validated = $request->validated();

        
        // Step 2: Sanitize with HTMLPurifier - Professional XSS Protection
        $validated['name'] = Purifier::clean($validated['name'], 'text');
        $validated['email'] = filter_var($validated['email'], FILTER_SANITIZE_EMAIL);
        $validated['content'] = Purifier::clean($validated['content'], 'comment');

        // Step 3: Additional Security Checks

        // Check for URLs (prevent spam and phishing)
        if(preg_match('/\b(?:https?:\/\/|www\.)/i', $validated['content'])) {
            return back()
            ->withInput()
            ->withErrors(['content' => 'URLs are not allowed in comments.']);
        }

        // Check for email addresses in content (prevent harvesting)
        if(preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $validated['content'] )) {
            return back()
            ->withInput()
            ->withErrors(['content' => 'Email addresses are not allowed in comment text.']);
        }

        // Check minimum word count (prevent spam)
        if(str_word_count($validated['content']) < 3) {
            return back()
            ->withInput()
            ->withErrors(['content' => 'Please provide a meaningful comment (at least 3 words).']);
        }


        // Check for excessive repeated characters (spam patterns)
        if(preg_match('/(.)\1{5,}/', $validated['content'])) {
            return back()
            ->withInput()
            ->withErrors(['content' => 'Comment contains suspicious repeated characters.']);
        }

        // Step 4: Duplicate Detection (24-hour window)
        $isDuplicate = Comment::where('email' , validated['email'])
            ->where('post_id' , $post->id)
            ->where('content', $validated['content'])
            ->where('created_at', '>', now()->subDay())
            ->exists();

        if($isDuplicate) {
            return back()->with('info', 'This comment has already been submitted.');
        }    

        // Step 5: Rate Limiting Check ( same email within 5 minutes )
        $recentComments = Comment::where('email' , $validated['email'])
            ->where('created_at', '>', now()->subMinutes(5))
            ->count();

        if($recenComments >= 3) {
            return back()
            ->withErrors(['email' => 'You are posting too quickly. Please wait a few minutes.']);
        }    

        // Step 6: Prepare and Save Comment
        $validated['post_id'] = $post->id;
        $validated['approved'] = false; // Requires admin approval
        $validated['ip_address'] = $request->ip(); // Track for moderation

        try {
            Comment::create($validated);
            
            //Increment rate limiter ( 5-minute decay)
            RateLimiter::hit($rateLimitKey, 300);

            return back()->with('success', 'Comment submitted successfully! It will appear after approval.');
        } catch(\Exception $e) {
            // Log error for debugging
            \Log::error('Comment submission failed: ' . $e->getMessage());

            return back()
            ->withInput()
            ->withErrors(['error' => 'Failed to submit commment. Please try again.']);
        }

        /**
         * Display all commments for admin review
         * Shows pending and approved comments with post information
         */
    }
  
    public function index()
    {
        // Authorization: ensures the user has permission to view comments.
        $this->authorize('viewAny', Comment::class);
        
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
        $this->authorize('approve', $comment);

        $comment->update(['approved' => true]);

        return back()->with('success' , 'Comment approved!');

        // updates the comment's approved field to true.
        // redirects back with a success message.
        // this is how an admin makes a comment visible on the public site.
    }
    // delete comment
    public function destroy (Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();
        // delete the comment from the database
        return back()->with('success', 'Comment deleted!');
        // redirect back with a success message.
        // this is how an admin removes unwanted comments.
    }
    

}
