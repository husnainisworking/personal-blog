<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
    'name',
    'slug',
    'description'
    ];

 //That is a safety list of which fields can be mass-assigned.
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

// That says one category can have many posts.
// Example: Category = "Laravel" -> Posts = "Routing Basics", "Middleware Explained",
// "Blade Templates"































}
