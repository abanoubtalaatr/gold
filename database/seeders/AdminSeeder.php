<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'superadmin2@admin.com')->first();
        if (!$user) {
            //i want to create a super admin user with the following credentials: and give it role super admin
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin2@admin.com',
                'password' => Hash::make('password'),
            ]);
            
        }
        $user->update([
            'email' => 'superadmin2@admin.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('superadmin');
    }

}
