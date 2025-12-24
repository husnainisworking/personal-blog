<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Cache duration (in seconds)
    const POSTS_CACHE_DURATION = 3600; // 1 hour

    const CATEGORIES_CACHE_DURATION = 86400; // 24 hours

    const TAGS_CACHE_DURATION = 86400; // 24 hours

    const POST_DETAIL_CACHE_DURATION = 3600; // 1 hour

    /**
     * Get all published posts with caching.
     */
    public static function getPublishedPosts($page = 1, $perPage = 10)
    /**
     * $page - current page number
     * $perPage - number of posts per page
     * This method retrieves published posts from the cache if available,
     * otherwise it fetches from the database and caches the result.
     */
    {
        $cacheKey = "posts.published.page.{$page}.perPage.{$perPage}";
        /**
         * Generate a unique cache key based on the page number and posts per page.
         */

        return Cache::remember($cacheKey, self::POSTS_CACHE_DURATION, function () use ($perPage) {
            return \App\Models\Post::with(['user', 'category', 'tags'])
                ->published()
                ->latest('published_at')
                ->paginate($perPage);
            // Fetch published posts with related user, category, and tags, ordered by published date.
        });
    }

    /**
     * Get single post with caching.
     */
    public static function getPost($slug)
    {

        $cacheKey = "post.{$slug}";
        // Syntax for generating a unique cache key for a single post based on its slug.

        return Cache::remember($cacheKey, self::POST_DETAIL_CACHE_DURATION, function () use ($slug) {

            return \App\Models\Post::with(['user', 'category', 'tags', 'approvedComments'])
                ->where('slug', $slug)
                ->firstOrFail();

        });
    }

    /**
     * Get all categories with post counts
     */
    public static function getCategories()
    {
        return Cache::remember('categories.all', self::CATEGORIES_CACHE_DURATION, function () {
            return \App\Models\Category::withCount('posts')->get();
        });
    }

    /**
     * Get posts by category
     */
    public static function getPostsByCategory($categorySlug, $page = 1)
    {
        $cacheKey = "category.{$categorySlug}.posts.page.{$page}";

        return Cache::remember($cacheKey, self::POSTS_CACHE_DURATION, function () use ($categorySlug) {

            $category = \App\Models\Category::where('slug', $categorySlug)->firstOrFail();

            return Post::where('category_id', $category->id)
                ->published()
                ->with(['user', 'tags'])
                ->latest('published_at')
                ->paginate(10);
        });

    }

    /**
     * Get posts by tag
     */
    public static function getPostsByTag($tagSlug, $page = 1)
    {
        $cacheKey = "tag.{$tagSlug}.posts.page.{$page}";

        return Cache::remember($cacheKey, self::POSTS_CACHE_DURATION, function () use ($tagSlug) {

            $tag = \App\Models\Tag::where('slug', $tagSlug)->firstOrFail();

            // Use whereHas to query through the relationship with published scope

            return Post::whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
                ->published()
                ->with(['user', 'category'])
                ->latest('published_at')
                ->paginate(10);
        });
    }

    /**
     * Clear all post-related caches
     */
    public static function clearPostCaches()
    {

        Cache::flush();
        // flush came from laravel cache facade to clear all caches
    }

    /**
     * Clear specific post cache
     */
    public static function clearPostCache($slug)
    {
        Cache::forget("post.{$slug}");
        Cache::flush();
    }

    /**
     * Clear all category caches
     */
    public static function clearCategoryCaches()
    {
        Cache::flush();
    }

    /**
     * Clear all tag caches
     */
    public static function clearTagCaches()
    {
        Cache::flush();
    }

    /**
     * Clear all caches (use sparingly!), sparingly means use it only when necessary
     */
    public static function clearAllCaches()
    {
        Cache::flush();
    }
}
