<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'gold_piece_id', 
        'branch_id',
        'total_price', 
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goldPiece(): BelongsTo
    {
        return $this->belongsTo(GoldPiece::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}