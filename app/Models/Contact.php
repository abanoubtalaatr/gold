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

    public function scopeFilter($query, array $filters)
{
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where(function ($query) use ($search) {
            $query->where('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%')
                  ->orWhere('phone', 'like', '%'.$search.'%')
                  ->orWhere('subject', 'like', '%'.$search.'%')
                  ->orWhere('message', 'like', '%'.$search.'%');
        });
    })->when($filters['type'] ?? null, function ($query, $type) {
        if ($type === 'rental') {
            $query->whereNotNull('rental_order_id');
        } elseif ($type === 'sale') {
            $query->whereNotNull('sale_order_id');
        } else {
            $query->whereNull('rental_order_id')->whereNull('sale_order_id');
        }
    })->when($filters['status'] ?? null, function ($query, $status) {
        if ($status === 'read') {
            $query->where('read', true);
        } elseif ($status === 'unread') {
            $query->where('read', false);
        } elseif ($status === 'replied') {
            $query->whereNotNull('reply');
        }
    });
}
}