<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create vendor role
        Role::create(['name' => 'vendor']);
        // i want to set role permissions to vendor


        // You can add permissions for vendors here if needed
        // Example:
        // Permission::create(['name' => 'manage products']);
        // $vendorRole = Role::findByName('vendor');
        // $vendorRole->givePermissionTo('manage products');
    }
}