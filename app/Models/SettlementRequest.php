<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettlementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'status',
        'admin_notes'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}