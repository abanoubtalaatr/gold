<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'message',
        'subject',
        'phone',
        'read',
        'read_at',
        'lat',
        'lng',
        'user_id',
        'reply',
        'sale_order_id',
        'rental_order_id',
    ];


    public function getStatus()
    {
        return $this->read == 1 ? __('admin.read') : __('admin.not_read');
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleOrder()
    {
        return $this->belongsTo(OrderSale::class, 'sale_order_id');
    }

    public function rentalOrder()
    {
        return $this->belongsTo(OrderRental::class, 'rental_order_id');
    }
}
