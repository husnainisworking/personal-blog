<?php

namespace App\Models;

use App\Services\CacheService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory
    // HasFactory means that this model can use Laravel's factory feature to generate test data.
        , SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    // converts the published_at column into a Carbon datetime object.

    // Boot method to handle cache invalidation, means when a post is created, updated, or deleted, the cache for 'posts' is cleared.
    protected static function boot()
    // protected is being used so that this method can only be accessed within this class and its subclasses.
    {
        parent::boot();
        // calling because we are overriding the boot method of the parent Model class.

        // Clear cache when post is created
        static::created(function ($post) {
            CacheService::clearPostCaches();
        });

        // Because we want to clear cache on create, update, delete, Because the data has changed, so the cached version is no longer valid.
        static::updated(function ($post) {

            CacheService::clearPostCaches();
            CacheService::clearPostCache($post->slug);

            if ($post->isDirty('status')) {
                CacheService::clearPostCache($post->getOriginal('slug'));
            }
            // isDirty checks if the 'status' attribute has changed during the update
        });

        // Clear cache when post is deleted
        static::deleted(function ($post) {
            CacheService::clearPostCaches();
            CacheService::clearPostCache($post->slug);
            // ($post->slug) is used to identify which specific post's cache to clear.
        });

        // Clear cache when post is restored
        static::restored(function ($post) {
            CacheService::clearPostCaches();
        });

    }

    // now going to discuss relationships
    // user(author)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // each post belongs to one user.
    }

    // category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
        // each post belongs to one category.
    }

    // tags
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    // a post can have many tags, and a tag can belong to many posts (pivot table post_tag).

    // comments
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    // a post can have many comments.

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('approved', true);
    }

    // shortcut to get only comments that are approved.

    /**
     *  Builder means that this method returns a query builder instance.
     *  Query builder allows you to build database queries programmatically.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
    // this is a custom query scope.

    public function scopeDraft(Builder $query): Builder
    // scope to filter draft posts
    {
        return $query->where('status', 'draft');
    }

    // Add more useful scopes
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>', now());
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByTag(Builder $query, int $tagId): Builder
    {
        return $query->whereHas('tags', function ($q) use ($tagId) {
            $q->where('tags.id', $tagId);
        });
    }

    public function scopeByAuthor(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function isPublished(): bool
    {
        return $this->status === 'published'
        && $this->published_at !== null
        && $this->published_at->isPast();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'published'
        && $this->published_at !== null
        && $this->published_at->isFuture();
    }
}
