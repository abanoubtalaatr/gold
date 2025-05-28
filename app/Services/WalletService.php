<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function getWallet(User $user): Wallet
    {
        return $user->wallet ?? $this->createWallet($user);
    }

    public function createWallet(User $user): Wallet
    {
        return $user->wallet()->create([
            'balance' => 0,
            'pending_balance' => 0,
            'total_earned' => 0,
        ]);
    }

    public function credit(User $user, float $amount, string $description, string $status = 'completed'): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $status) {
            $wallet = $this->getWallet($user);
            return $wallet->credit($amount, $description, $status);
        });
    }

    public function debit(User $user, float $amount, string $description): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            $wallet = $this->getWallet($user);
            return $wallet->debit($amount, $description);
        });
    }

    public function getTransactions(User $user, array $filters = [])
    {
        $wallet = $this->getWallet($user);
        
        $query = $wallet->transactions()->latest();
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->paginate(15);
    }
} 