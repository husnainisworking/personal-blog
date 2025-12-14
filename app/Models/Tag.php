<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
      'name',
      'slug'
    ];

    // A tag belongs to many posts
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
