<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'name',
        'email',
        'content',
        'approved',
        'ip_address',
    ];


    protected $casts = [
        'approved' => 'boolean',
    ];

    //comment belongs to a post
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    // Scopes for easy querying
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('approved', false);
    }
}
