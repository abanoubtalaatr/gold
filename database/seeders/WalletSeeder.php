<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
    $wallet =  Wallet::updateOrCreate([
        'user_id' => User::where('email', 'admin@admin.com')->first()->id,
      ], [
        'balance' => 0,
        'pending_balance' => 0,
        'total_earned' => 0,
      ]);
    }
} 