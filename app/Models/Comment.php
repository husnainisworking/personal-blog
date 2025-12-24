<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

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
        'deleted_at' => 'datetime',
    ];

    // comment belongs to a post
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
