<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendor = User::updateOrCreate(
            ['email' => 'vendor@gold-station.com'],
            [
                'name' => 'Gold Station Vendor',
                'email' => 'vendor@gold-station.com',
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );

        $vendor->syncRoles(roles: ['vendor']);

    }
}
