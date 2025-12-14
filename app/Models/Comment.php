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
        'approved'
    ];


    protected $casts = [
        'approved' => 'boolean',
    ];

    //comment belongs to a post
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
