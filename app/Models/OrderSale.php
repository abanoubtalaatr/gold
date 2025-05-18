<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderSale extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'gold_piece_id', 'total_price', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goldPiece()
    {
        return $this->belongsTo(GoldPiece::class);
    }
}