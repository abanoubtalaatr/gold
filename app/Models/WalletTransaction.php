<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'status',
        'transactionable_type',
        'transactionable_id'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
