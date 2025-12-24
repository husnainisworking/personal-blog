<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Posts
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',

            // Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            // Tags
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',

            // Comments
            'view comments',
            'approve comments',
            'delete comments',

            // System
            'view dashboard',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. Super Admin - Has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Admin - Can manage content but not users
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([

            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view tags', 'create tags', 'edit tags', 'delete tags',
            'view comments', 'approve comments', 'delete comments',
            'view dashboard',
        ]);

        // 3. Editor - Can create and edit posts but not delete
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->syncPermissions([
            'view posts', 'create posts', 'edit posts',
            'view categories', 'view tags',
            'view comments',
            'view dashboard',
        ]);

        // 4. Moderator - Can only manage comments
        $moderator = Role::firstOrCreate(['name' => 'moderator']);
        $moderator->syncPermissions([
            'view posts', 'view categories', 'view tags',
            'view comments', 'approve comments', 'delete comments',
            'view dashboard',
        ]);

        $this->command->info('Roles and permissions have been seeded successfully.');
        // command->info is used to output information to the console, it differs from echo or print as it is specifically designed for Laravel's command line interface.
    }
}
