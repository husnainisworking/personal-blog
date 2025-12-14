<?php


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;



// Home route
Route::get('/', function() {
   $posts = \App\Models\Post::published()
       ->with(['user', 'category', 'tags'])
       ->latest('published_at')
       ->paginate(10);
   return view('welcome', compact('posts'));
})->name('home');
/** URL: /(homepage)
 * Fetches published posts only
 * Loads relationships (user, category, tags) to avoid extra queries.
 * Orders by newest published_at.
 * Paginates results (10 per page).
 * Sends data to welcome.blade.php
 * This is blog's homepage showing the latest posts.
 */


// Public blog post view
Route::get('/posts/{post:slug}' , [PostController::class, 'show'])
    ->name('posts.public.show');
/** URL: /posts/{slug} (e.g., /posts/laravel-basics
 * Uses route model binding by slug ({post:slug})
 * Calls PostController@show , displays a single post.
 */

// Public category view
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
    ->name('categories.show');
// URL: /categories/{slug} (e.g., /categories/laravel).
// Calls CategoryController@show.
// Shows all published posts inside a specific category.

// Public tag view
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])
->name('tags.show');
/**
 * URL: /tags/{slug} (e.g., /tags/php)
 * Calls TagController@show
 * Shows all published posts linked to a specific tag.
 */

// Public comment submission
Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('comments.store');
/**
 * URL: /posts/{post}/comments, {post} is a route parameter -- Laravel will inject the Post model based on the ID in the URL.
 * Calls CommentController@store.
 * Visitors can submit comments under a post (saved but awaiting admin approval).
* POST /posts/5/comments , laravel will find Post::find(5) and pass it to the controller.
 */

// Search Route
Route::get('/search' , function() {
   $query = request('q');
   // user types "Laravel" in blog's search box.
    // browser hits /search?q=Laravel
   $posts = \App\Models\Post::published()
       ->where(function($q) use ($query){
         $q->where('title', 'like', "%{$query}%")
             // %{$query}% means "anywhere in the string"
                 // search posts by title/content.
             ->orWhere('content', 'like', "%{$query}%");
       })
       ->with(['user', 'category', 'tags'])
       ->latest('published_at')
       ->paginate(10);
   return view('search', compact('posts', 'query'));
})->name('search');

//Admin Routes (Protected)
// All routs inside this group require the user to be logged in (auth middleware).
// if not authenticated -> redirected to login.
Route::middleware(['auth','2fa.verified', 'admin'])->group(function () {
   //Dashboard
    Route::get('/dashboard' , function () {
        $stats = [
            'total_posts' => \App\Models\Post::count(),
            'published_posts' => \App\Models\Post::where('status', 'published')->count(),
            'draft_posts' => \App\Models\Post::where('status', 'draft')->count(),
            'total_comments' => \App\Models\Comment::count(),
            'pending_comments' => \App\Models\Comment::where('approved', false)->count(),
            'total_categories' => \App\Models\Category::count(),
            'total_tags' => \App\Models\Tag::count(),
        ];
        return view('dashboard' , compact('stats'));
    })->name('dashboard');
/**
 * URL: /dashboard
 * Collect site statistics:
 *  Total comments, pending comments.
 *  Total categories, total tags.
 * Passes stats to dashboard.blade.php , this is the admin overview page
 */
        //Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Edit profile -> /profile (GET)
     *  Update profile -> /profile (PATCH).
     *  Delete account -> /profile (DELETE).
     * Lets the logged-in user manage their own profile.
     */

    //Post management
    Route::resource('admin/posts', PostController::class)->except(['show']);
    /**
     * Generates all CRUD routes for posts (index, create, store, edit, update, destroy).
     *  Excludes show because public post viewing is handled separately.
     *  Admin can manage posts.
     *  This generates a full set of RESTful routes for a resource (in this case, posts under the admin prefix).
     *  all these routes point to methods inside PostController.
     *
     */

    //Category management
    Route::resource('admin/categories', CategoryController::class)->except(['show']);
    /**
     * Excludes show (public view handled separately), Admin can manage categories.
     */

    // Tag management
    // Admin can manage tags.
    Route::resource('admin/tags', TagController::class)->except(['show']);

    //Comment management
    Route::resource('admin/comments', CommentController::class)->only(['index', 'destroy']);

    //Custom approve route
    Route::post('admin/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    /**
     * List comments -> /admin/comments.
     * Approve comment -> /admin/comments/{comment}/approve
     * Delete comment -> /admin/comments/{comment}.
     * Admin can moderate comments.
     */


        Route::get('2fa/verify', [TwoFactorController::class, 'show'])->name('2fa.show');
        Route::post('2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');

        // New route for resending code
        Route::post('2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');

});

require __DIR__. '/auth.php';
//includes Laravel Breeze/Fortify/Jetstream auth routes (login, register, password reset, etc.)












































































