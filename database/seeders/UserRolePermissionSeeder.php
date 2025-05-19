<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserRolePermissionSeeder extends Seeder
{
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
            'branches' => ['create', 'read', 'update', 'delete', 'view'],
            // Vendor-prefixed permissions
            'vendor roles' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
            'vendor permissions' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
            'vendor users' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
            'vendor branches' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
        ];

        foreach ($permissions as $group => $actions) {
            foreach ($actions as $action) {
                Permission::findOrCreate("$action $group", 'web');
            }
        }

        // Roles
        $roles = [
            'vendor' => [
                'vendor create roles', 'vendor read roles', 'vendor view roles', 'vendor update roles', 'vendor delete roles',
                'vendor create permissions', 'vendor read permissions', 'vendor view permissions', 'vendor update permissions', 'vendor delete permissions',
                'vendor create users', 'vendor read users', 'vendor view users', 'vendor update users', 'vendor delete users',
                'vendor create branches', 'vendor read branches', 'vendor view branches', 'vendor update branches', 'vendor delete branches',
            ],
            'superadmin' => Permission::pluck('name')->toArray(),
            'admin' => [
                'create roles', 'read roles', 'view roles', 'update roles', 'delete roles',
                'create permissions', 'read permissions', 'view permissions', 'update permissions', 'delete permissions',
                'create users', 'read users', 'view users', 'update users', 'delete users',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissions);
        }

        // Users
        $users =[
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