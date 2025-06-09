<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete all permissions and related data (commented out to prevent accidental data loss)
        // DB::table('model_has_permissions')->delete();
        // DB::table('model_has_roles')->delete();
        // DB::table('role_has_permissions')->delete();
        // Permission::query()->delete();
        // Role::query()->delete();

        // Permissions
        $permissions = [
            // Standard permissions
            'roles' => ['create', 'read', 'update', 'delete', 'view'],
            'permissions' => ['create', 'read', 'update', 'delete', 'view'],
            'users' => ['create', 'read', 'update', 'delete', 'view'],
            'banners' => ['create', 'read', 'update', 'delete', 'view'],
            'settings' => ['read', 'update'],
            'contacts' => ['read', 'update'],
            'static_pages' => ['read', 'update'],
            'branches' => ['create', 'read', 'update', 'delete', 'view'],
            'gold-pieces' => ['create', 'read', 'update', 'delete', 'view'],
            'companies' => ['create', 'read', 'update', 'delete', 'view'],
            'complaints' => ['create', 'read', 'update', 'delete', 'view'],
            'dashboard' => ['view'],
            'vendors' => ['create', 'read', 'update', 'delete', 'view'],
            'cities' => ['create', 'read', 'update', 'delete', 'view'],
            'app_settings' => ['read', 'update'],
            'settlement-requests' => ['read', 'create', 'update', 'delete'],
            'reports' => ['create', 'read', 'update', 'delete', 'view'],
            'wallet'=>['read','update'],
            // Vendor-prefixed permissions
            'vendor' => [
                'roles' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
                'permissions' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
                'users' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
                'branches' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
                'orders' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
                'rental-requests' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
                'dashboard' => ['vendor view'],
                'settlement-requests' => ['vendor read', 'vendor create'],
                'reports' => ['vendor create', 'vendor read', 'vendor update', 'vendor delete', 'vendor view'],
                'wallet' => ['vendor read', 'vendor update'],
            ],
        ];

        // Create permissions
        foreach ($permissions as $group => $actions) {
            if ($group === 'vendor') {
                // Handle vendor-prefixed permissions (e.g., 'vendor create roles')
                foreach ($actions as $subGroup => $subActions) {
                    foreach ($subActions as $action) {
                        Permission::findOrCreate("$action $subGroup", 'web');
                    }
                }
            } else {
                // Handle standard permissions (e.g., 'create roles')
                foreach ($actions as $action) {
                    Permission::findOrCreate("$action $group", 'web');
                }
            }
        }

        // Roles
        $roles = [
            'vendor' => [
                'vendor create roles', 'vendor read roles', 'vendor view roles', 'vendor update roles', 'vendor delete roles',
                'vendor create permissions', 'vendor read permissions', 'vendor view permissions', 'vendor update permissions', 'vendor delete permissions',
                'vendor create users', 'vendor read users', 'vendor view users', 'vendor update users', 'vendor delete users',
                'vendor create branches', 'vendor read branches', 'vendor view branches', 'vendor update branches', 'vendor delete branches',
                'vendor create orders', 'vendor read orders', 'vendor view orders', 'vendor update orders', 'vendor delete orders',
                'vendor create rental-requests', 'vendor read rental-requests', 'vendor view rental-requests', 'vendor update rental-requests', 'vendor delete rental-requests',
                'vendor view dashboard',
                'vendor read settlement-requests', 'vendor create settlement-requests',
                'vendor create reports', 'vendor read reports', 'vendor view reports', 'vendor update reports', 'vendor delete reports',
                'vendor read wallet', 'vendor update wallet',
            ],
            'superadmin' => [
                'create roles', 'read roles', 'view roles', 'update roles', 'delete roles',
                'create permissions', 'read permissions', 'view permissions', 'update permissions', 'delete permissions',
                'create users', 'read users', 'view users', 'update users', 'delete users',
                'create banners', 'read banners', 'view banners', 'update banners', 'delete banners',
                'read settings', 'update settings',
                'read contacts', 'update contacts',
                'read static_pages', 'update static_pages',
                'create branches', 'read branches', 'view branches', 'update branches', 'delete branches',
                'view dashboard',
                'read app_settings', 'update app_settings',
                'create cities', 'read cities', 'view cities', 'update cities', 'delete cities',
                'create branches', 'read branches', 'view branches', 'update branches', 'delete branches',
                'read settlement-requests', 'create settlement-requests','update settlement-requests', 'delete settlement-requests',
                'create vendors', 'read vendors', 'view vendors', 'update vendors', 'delete vendors',
                'create reports', 'read reports', 'view reports', 'update reports', 'delete reports',
                'read wallet', 'update wallet',
            ],
            'admin' => [
                'create roles', 'read roles', 'view roles', 'update roles', 'delete roles',
                'create permissions', 'read permissions', 'view permissions', 'update permissions', 'delete permissions',
                'create users', 'read users', 'view users', 'update users', 'delete users',
                'view dashboard',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName, 'web');
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