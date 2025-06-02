<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'state_id',
        'state_code',
        'country_id',
        'country_code',
        'latitude',
        'longitude',
        'flag',
        'wikiDataId',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    // public function getStatusAttribute($value)
    // {
    //     return $value ? 'active' : 'inactive';
    // }

    // public function setStatusAttribute($value)
    // {
    //     $this->attributes['status'] = $value === 'active';
    // }


    public function vendors()
    {
        return $this->hasMany(User::class)->whereHas('roles', function ($q) {
            $q->where('name', 'vendor');
        });
    }

    public function activeVendors()
    {
        return $this->vendors()->where('is_active', true);
    }

    public function usersWithOrders()
    {
        return $this->users()
            ->where(function ($query) {
                $query->has('orderRentals')
                    ->orHas('orderSales');
            });
    }

    public function activeVendorsWithOrders()
    {
        return $this->activeVendors()
            ->where(function ($query) {
                $query->has('orderRentals')
                    ->orHas('orderSales');
            });
    }
}