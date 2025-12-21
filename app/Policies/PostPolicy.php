<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine if user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view posts');
    }

    /**
     * Determine if user can view a specific post.
     */
    public function view(User $user, Post $post): bool
    {
        return $user->can('view posts');
    }

    /**
     * Determine if user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->can('create posts');
    }

    /**
     * Determine if user can update the post.
     * User can edit if:
     * - They own the post, OR
     * - They have 'edit posts' permission (admin/super-admin)
     */
    public function update(User $user, Post $post): bool
    {
        // Super admins and admins can delete any post
        if ($user->can('delete posts')) {
            return true;
        }

        // Users can delete their own posts (if they created it)
        return $user->id === $post->user_id;
    }

    /**
     * Determine if user can delete the post
     * Only post owner OR users with 'delete posts' permission
     */
    public function delete(User $user, Post $post): bool
    {
        // Super admins and admins can delete any post
        if ($user->can('delete posts')) {
            return true;
        }

        // Users can delete their own posts(if they created it)
        return $user->id === $post->user_id;
    }

    /**
     * Determine if user can publish posts.
     */
    public function publish(User $user): bool
    {
        return $user->can('publish posts');
    }

    /**
     * Determine if user can restore a soft-deleted post.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('delete posts') || $post->user_id === $user->id;
    }

    /**
     * Determine if the user can permanently delete a post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('delete posts');
        // Only users with 'delete posts' permission can force delete.

    }
}
