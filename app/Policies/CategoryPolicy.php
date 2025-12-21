<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine if user can view categories
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view categories');
    }

    /**
     * Determine if user can create categories
     */
    public function create(User $user): bool
    {
        return $user->can('create categories');
    }

    /**
     * Determine if user can update categories
     */
    public function update(User $user, Category $category): bool
    {
        return $user->can('edit categories');
    }

    /**
     * Determine if user can delete categories
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->can('delete categories');
    }
}
