<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserRolePermissionSeeder;
use App\Models\Setting;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);
        $this->call([UserRolePermissionSeeder::class]);
        $this->call([SettingsTableSeeder::class]);
        $this->call([PageSeeder::class]);
        $this->call([FaqSeeder::class]);
        $this->call([VendorSeeder::class]);
        $this->call([NotificationSeeder::class]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
