<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Factories\NotificationFactory;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create some test users
        $users = User::take(5)->get();
        
        if ($users->isEmpty()) {
            $users = User::factory(5)->create();
        }

        // Create notifications for each user
        foreach ($users as $user) {
            // Create a mix of read and unread notifications
            // Unread notifications (5 per user)
            DB::table('notifications')->insert(
                NotificationFactory::new()
                    ->count(5)
                    ->unread()
                    ->forUser($user)
                    ->make()
                    ->map(fn ($notification) => (array) $notification)
                    ->all()
            );

            // Read notifications (10 per user)
            DB::table('notifications')->insert(
                NotificationFactory::new()
                    ->count(10)
                    ->read()
                    ->forUser($user)
                    ->make()
                    ->map(fn ($notification) => (array) $notification)
                    ->all()
            );
        }
    }
} 