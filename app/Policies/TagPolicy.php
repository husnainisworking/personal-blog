<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
{
    /**
     * Determine if user can view tags
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view tags');
    }

    /**
     * Determine if user can create tags.
     */
    public function create(User $user): bool
    {
        return $user->can('create tags');
    }

    /**
     * Determine if user can update tags.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $user->can('edit tags');
    }

    /**
     * Determine if user can delete tags.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $user->can('delete tags');
    }
}
