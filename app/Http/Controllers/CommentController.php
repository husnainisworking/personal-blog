<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\QueryException;
use Mews\Purifier\Facades\Purifier;

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

        $validated = $request->validated();

        // Step 2: Sanitize with HTMLPurifier - Professional XSS Protection
        $validated['name'] = Purifier::clean($validated['name'], 'text');
        $validated['email'] = filter_var($validated['email'], FILTER_SANITIZE_EMAIL);
        $validated['content'] = Purifier::clean($validated['content'], 'comment');

        // Step 3: Additional Security Checks

        // If URLs are NOT allowed AND a URL is found, then show error
        if (! config('comments.spam_prevention.allow_urls') &&
            preg_match('/\b(?:https?:\/\/|www\.)/i', $validated['content'])) {
            return back()
                ->withInput()
                ->withErrors(['content' => 'URLs are not allowed in comments.']);
        }

        // Check for email addresses in content (prevent harvesting)
        if (! config('comments.spam_prevention.allow_emails') &&
            preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $validated['content'])) {
            return back()
                ->withInput()
                ->withErrors(['content' => 'Email addresses are not allowed in comment text.']);
        }

        // Check minimum word count (prevent spam)
        $minWords = config('comments.spam_prevention.min_word_count');
        if (str_word_count($validated['content']) < $minWords) {
            return back()
                ->withInput()
                ->withErrors(['content' => "Please provide a meaningful comment (at least {$minWords} words)."]);
        }

        // Check for excessive repeated characters (spam patterns)
        // Builds regex pattern dynamically: /(.)\1{5,}/ means "any character repeated 5+ times"
        $maxRepeated = config('comments.spam_prevention.max_repeated_chars');
        if (preg_match('/(.)\1{'.$maxRepeated.',}/', $validated['content'])) {
            return back()
                ->withInput()
                ->withErrors(['content' => 'Comment contains suspicious repeated characters.']);
        }

        // Step 4: Duplicate Detection (24-hour window), subHours() is flexible, you can set 24 hours, 12 hours, 48 hours, etc.
        $duplicateCheckHours = config('comments.spam_prevention.duplicate_check_hours');
        $isDuplicate = Comment::where('email', $validated['email'])
            ->where('post_id', $post->id)
            ->where('content', $validated['content'])
            ->where('created_at', '>', now()->subHours($duplicateCheckHours))
            ->exists();

        if ($isDuplicate) {
            return back()->with('info', 'This comment has already been submitted.');
        }

        // Step 5: Rate Limiting Check ( same email within 5 minutes )
        $maxAttempts = config('comments.rate_limit.max_attempts');
        $decayMinutes = config('comments.rate_limit.decay_minutes');

        $recentComments = Comment::where('email', $validated['email'])
            ->where('created_at', '>', now()->subMinutes($decayMinutes))
            ->count();

        if ($recentComments >= $maxAttempts) {
            return back()
                ->withErrors(['email' => 'You are posting too quickly. Please wait a few minutes.']);
        }

        // Step 6: Prepare and Save Comment, Allow auto-approval if configured in .env, Only stores IP if tracking is enabled.
        $validated['post_id'] = $post->id;
        $validated['approved'] = config('comments.moderation.auto_approve', false);

        if (config('comments.moderation.track_ip')) {
            $validated['ip_address'] = $request->ip();
        }

        try {
            Comment::create($validated);

            $message = config('comments.moderation.auto_approve')
            ? 'Comment posted successfully!'
            : 'Comment submitted successfully! It will appear after approval.';

            return back()->with('success', $message);

        } catch (QueryException $e) {
            // Specific: Database errors(constraint violations, connection issues, etc)
            \Log::error('Database error while creating comment', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'email' => $validated['email'],
                'post_id' => $post->id,
                'ip' => $request->ip(),
            ]);

            // Check for specific database error codes, duplicate entry error
            if ($e->getCode() === '23000') {
                // Integrity constraint violation (duplicate, foreign key, etc.)
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'This comment could not be saved due to a data conflict.']);
            }

            // General database error
            return back()
                ->withInput()
                ->withErrors(['error' => 'A database error occurred. Please try again later.']);
        }

        /**
         * Display all comments for admin review
         * Shows pending and approved comments with post information
         */
    }

    public function index()
    {
        // Authorization: ensures the user has permission to view comments.
        $this->authorize('viewAny', Comment::class);

        // Change pagination in one config file
        $perPage = config('comments.pagination.per_page');

        $comments = Comment::with(['post' => function ($query) {
            $query->withTrashed(); // Include soft-deleted posts
        }])
            ->latest()
            ->paginate($perPage);

        return view('comments.index', compact('comments'));
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

        return back()->with('success', 'Comment approved!');

        // updates the comment's approved field to true.
        // redirects back with a success message.
        // this is how an admin makes a comment visible on the public site.
    }

    // delete comment
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        // delete the comment from the database
        return back()->with('success', 'Comment deleted!');
        // redirect back with a success message.
        // this is how an admin removes unwanted comments.
    }

    /**
     * Display trashed (soft-deleted) comments
     */
    public function trashed()
    {
        $this->authorize('viewAny', Comment::class);
        // viewAny policy allows viewing trashed comments as well.

        // Both index() and trashed() use same pagination value
        $perPage = config('comments.pagination.per_page');

        $comments = Comment::onlyTrashed()
            ->with(['post' => function ($query) {
                $query->withTrashed();
            }])
            // here closure loads related posts including soft-deleted ones.
            ->latest('deleted_at')
            ->paginate($perPage);

        return view('comments.trashed', compact('comments'));
        // view is helper to show trashed comments in comments/trashed.blade.php

    }

    /**
     * Restore a soft-deleted comment
     */
    public function restore($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);

        $this->authorize('restore', $comment);
        // authorize came from CommentPolicy, which came from AuthServiceProvider which came from middleware 'auth' in routes/web.php

        $comment->restore();

        return back()->with('success', 'Comment restored successfully!');
        // restore() is Eloquent method to un-delete a soft-deleted record.
    }

    /**
     * Permanently delete a comment
     */
    public function forceDelete($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $comment);

        $comment->forceDelete();

        return back()->with('success', 'Comment permanently deleted!');
    }
}
