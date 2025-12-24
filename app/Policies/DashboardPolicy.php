<?php

namespace App\Policies;

use App\Models\User;

class DashboardPolicy
{
    /**
     * Determine if user can view the dashboard.
     */
    public function viewDashboard(User $user): bool
    {
        // Users with 'view dashboard' permission  OR admin role can access
        return $user->can('view dashboard') || $user->hasRole('admin');

    }
}
