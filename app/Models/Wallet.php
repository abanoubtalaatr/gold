<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance',
        'total_earned',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function settlementRequests()
    {
        return $this->hasMany(SettlementRequest::class);
    }

    public function credit(float $amount, string $description, string $status = 'completed'): WalletTransaction
    {
        $transaction = $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'status' => $status,
            'transactionable_type' => 'system',
            'transactionable_id' => 0,
        ]);

        return $transaction;
    }

    public function debit(float $amount, string $description): WalletTransaction
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $transaction = $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'status' => 'completed',
            'transactionable_type' => 'system',
            'transactionable_id' => 0,
        ]);

        $this->decrement('balance', $amount);

        return $transaction;
    }
}
