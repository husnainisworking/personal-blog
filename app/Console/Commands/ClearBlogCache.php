<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class ClearBlogCache extends Command
{
    protected $signature = 'blog:cache-clear {type?}';
    //  type can be 'all', 'posts', 'comments', 'tags'

    protected $description = 'Clear blog caches (posts, categories, tags, or all)';
    // Means of clearing cache for blog-related data

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type') ?? 'all';
        // This method clears cache based on the type argument.

        match ($type) {

            'posts' => $this->clearPosts(),
            'categories' => $this->clearCategories(),
            'tags' => $this->clearTags(),
            'all' => $this->clearAll(),
            default => $this->error('Invalid type. Use: posts, categories, tags, or all.')
        };
    }

    private function clearPosts()
    {
        CacheService::clearPostCaches();
        $this->info('✅ Post caches cleared!');
        // $this->info means of displaying a success message in the console.
    }

    private function clearCategories()
    {
        CacheService::clearCategoryCaches();
        $this->info('✅ Category caches cleared!');
    }

    private function clearTags()
    {
        CacheService::clearTagCaches();
        $this->info('✅ Tag caches cleared!');
    }

    private function clearAll()
    {
        CacheService::clearAllCaches();
        $this->info('✅ All caches cleared!');
    }
}
