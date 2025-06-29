<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'type',
        'order_sale_id',
        'order_rental_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderSale()
    {
        return $this->belongsTo(OrderSale::class);
    }

    public function orderRental()
    {
        return $this->belongsTo(OrderRental::class);
    }
}
