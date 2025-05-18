<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'roles' => ['create', 'read', 'update', 'delete', 'view'],
            'permissions' => ['create', 'read', 'update', 'delete', 'view'],
            'users' => ['create', 'read', 'update', 'delete', 'view'],
            'banners' => ['create', 'read', 'update', 'delete', 'view'],
            'settings' => ['read', 'update'],
            'contacts' => ['read', 'update'],
            'static_pages' => ['read', 'update'],
        ];

        foreach ($permissions as $group => $actions) {
            foreach ($actions as $action) {
                Permission::findOrCreate("$action $group");
            }
        }

        // Roles
        $roles = [
            'superadmin' => Permission::pluck('name')->toArray(),
            'admin' => [
                'create roles', 'read roles', 'view roles', 'update roles',
                'create permissions', 'read permissions', 'view permissions',
                'create users', 'read users', 'view users', 'update users',
            ],
            'vendor' => [
                'create roles', 'read roles', 'view roles', 'update roles',
                'create permissions', 'read permissions', 'view permissions',
                'create users', 'read users', 'view users', 'update users',

            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($permissions);
        }

        // Users
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => config('app.admin_pass'),
                'role' => 'superadmin',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                ]
            );
            
            $user->syncRoles([$userData['role']]);
        }
    }
}
