<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');
/** URL: /(homepage)
 * Fetches published posts only
 * Loads relationships (user, category, tags) to avoid extra queries.
 * Orders by newest published_at.
 * Paginates results (10 per page).
 * Sends data to welcome.blade.php
 * This is blog's homepage showing the latest posts.
 */

// Public blog post view
Route::get('/posts/{post:slug}', [PostController::class, 'show'])
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
    ->middleware('spam', 'throttle:10,1')
    ->name('comments.store');
/**
 * URL: /posts/{post}/comments, {post} is a route parameter -- Laravel will inject the Post model based on the ID in the URL.
 * Calls CommentController@store.
 * Visitors can submit comments under a post (saved but awaiting admin approval).
 * POST /posts/5/comments , laravel will find Post::find(5) and pass it to the controller.
 */

// Search Route
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Soft delete routes for posts
Route::middleware(['auth'])->group(function () {

    // View trashed posts
    Route::get('/posts/trashed', [PostController::class, 'trashed'])
        ->name('posts.trashed');

    // Restore a trashed post
    Route::post('/posts/{id}/restore', [PostController::class, 'restore'])
        ->name('posts.restore');

    // Permanently delete a trashed post
    Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete'])
        ->name('posts.force-delete');
});

// Soft delete routes for comments
Route::middleware(['auth'])->group(function () {

    // View trashed comments
    Route::get('/comments/trashed', [CommentController::class, 'trashed'])
        ->name('comments.trashed');

    // Restore a trashed comment
    Route::post('/comments/{id}/restore', [CommentController::class, 'restore'])
        ->name('comments.restore');

    // Permanently delete a trashed comment
    Route::delete('/comments/{id}/force-delete', [CommentController::class, 'forceDelete'])
        ->name('comments.force-delete');
});

// Admin Routes (Protected)
// All routs inside this group require the user to be logged in (auth middleware).
// if not authenticated -> redirected to login.
// FIXED: Removed 'admin' middleware - authorization now handled by policies in controllers
Route::middleware(['auth', '2fa.verified', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    /**
     * URL: /dashboard
     * Collect site statistics:
     *  Total comments, pending comments.https://cineb.gg/
     *  Total categories, total tags.
     * Passes stats to dashboard.blade.php , this is the admin overview page
     */
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Edit profile -> /profile (GET)
     *  Update profile -> /profile (PATCH).https://cineb.gg/
     *  Delete account -> /profile (DELETE).
     * Lets the logged-in user manage their own profile.
     */

    // Post management
    Route::resource('admin/posts', PostController::class)->except(['show']);
    /**
     * Generates all CRUD routes for posts (index, create, store, edit, update, destroy).
     *  Excludes show because public post viewing is handled separately.
     *  Admin can manage posts.
     *  This generates a full set of RESTful routes for a resource (in this case, posts under the admin prefix).
     *  all these routes point to methods inside PostController.
     */

    // Category management
    Route::resource('admin/categories', CategoryController::class)->except(['show']);
    /**
     * Excludes show (public view handled separately), Admin can manage categories.
     */

    // Tag management
    // Admin can manage tags.
    Route::resource('admin/tags', TagController::class)->except(['show']);

    // Comment management
    Route::resource('admin/comments', CommentController::class)->only(['index', 'destroy']);

    // Custom approve route
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

require __DIR__.'/auth.php';
// includes Laravel Breeze/Fortify/Jetstream auth routes (login, register, password reset, etc.)
