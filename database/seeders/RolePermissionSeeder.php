<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    
    public function run(): void
    {
        //Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //Create permissions
        $permissions = [
            //Posts
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',

            //Categories
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',

            //Tags
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',

            //Comments
            'view comments',
            'approve comments',
            'delete comments',

            //System
            'view dashboard',
            'manage users',
        ];

        foreach ($permissions as $permission)
        {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        //1. Super Admin - Has all permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        //2. Admin - Can manage content but not users
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([

            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view tags', 'create tags', 'edit tags', 'delete tags',
            'view comments', 'approve comments', 'delete comments',
            'view dashboard',
        ]);

        //3. Editor - Can create and edit posts but not delete
        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 
            'view categories', 'view tags',
            'view comments',
            'view dashboard',
        ]);

        //4. Moderator - Can only manage comments
        $moderator = Role::create(['name' => 'moderator']);
        $moderator->givePermissionTo([
            'view posts', 'view categories', 'view tags',
            'view comments', 'approve comments', 'delete comments',
            'view dashboard',
        ]);
    }
}
