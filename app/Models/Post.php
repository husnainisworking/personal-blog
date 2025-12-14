<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
      'user_id',
      'category_id',
      'title',
      'slug',
      'excerpt',
      'content',
      'featured_image',
      'status',
      'published_at'
    ];

    protected $casts = [
      'published_at' => 'datetime',
    ];
    //converts the published_at column into a Carbon datetime object.

    //now going to discuss relationships
    //user(author)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
        //each post belongs to one user.
    }
    //category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
        //each post belongs to one category.
    }
    //tags
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
    // a post can have many tags, and a tag can belong to many posts (pivot table post_tag).

    //comments
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    // a post can have many comments.

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('approved', true);
    }
    //shortcut to get only comments that are approved.

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
    // this is a custom query scope.













}
