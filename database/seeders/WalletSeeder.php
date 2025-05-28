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
        // Create test users if they don't exist
        $users = User::factory(3)->create();

        foreach ($users as $user) {
            // Create wallet
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 1000.00,
                'pending_balance' => 200.00,
                'total_earned' => 1500.00,
            ]);

            // Create some transactions
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => 1000.00,
                'description' => 'Initial deposit',
                'status' => 'completed',
                'transactionable_type' => 'system',
                'transactionable_id' => 0,
            ]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => 500.00,
                'description' => 'Bonus credit',
                'status' => 'completed',
                'transactionable_type' => 'system',
                'transactionable_id' => 0,
            ]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => 500.00,
                'description' => 'Withdrawal',
                'status' => 'completed',
                'transactionable_type' => 'system',
                'transactionable_id' => 0,
            ]);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => 200.00,
                'description' => 'Pending reward',
                'status' => 'pending',
                'transactionable_type' => 'system',
                'transactionable_id' => 0,
            ]);
        }
    }
} 