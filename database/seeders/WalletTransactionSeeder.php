<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first client (user without roles)
        $client = User::doesntHave('roles')->first();
        
        if ($client) {
            // Create or get wallet for the client
            $wallet = $client->wallet()->firstOrCreate([
                'user_id' => $client->id
            ], [
                'balance' => 100.00,
                'pending_balance' => 0,
                'total_earned' => 0,
            ]);
            
            // Create sample transactions
            $transactions = [
                [
                    'type' => 'credit',
                    'amount' => 50.00,
                    'description' => 'Initial deposit',
                    'status' => 'completed',
                    'transactionable_type' => 'system',
                    'transactionable_id' => 1,
                ],
                [
                    'type' => 'debit',
                    'amount' => 25.00,
                    'description' => 'Purchase deduction',
                    'status' => 'completed',
                    'transactionable_type' => 'system',
                    'transactionable_id' => 1,
                ],
                [
                    'type' => 'credit',
                    'amount' => 75.00,
                    'description' => 'Refund request',
                    'status' => 'pending',
                    'transactionable_type' => 'system',
                    'transactionable_id' => 1,
                ],
                [
                    'type' => 'debit',
                    'amount' => 10.00,
                    'description' => 'Service fee',
                    'status' => 'completed',
                    'transactionable_type' => 'system',
                    'transactionable_id' => 1,
                ],
                [
                    'type' => 'credit',
                    'amount' => 200.00,
                    'description' => 'Bonus credit',
                    'status' => 'pending',
                    'transactionable_type' => 'system',
                    'transactionable_id' => 1,
                ],
            ];
            
            foreach ($transactions as $transactionData) {
                $wallet->transactions()->create($transactionData);
            }
            
            $this->command->info("Created wallet and sample transactions for client: {$client->name}");
        } else {
            $this->command->error('No clients found');
        }
    }
}
