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
        'admin_notes',
        'admin_id',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}