<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'vendor_id',
        'name',
        'city_id',
        'working_days',
        'working_hours',
        'services',
        'is_active',
        'address',
        'logo',
        'contact_number',
        'contact_email',
        'number_of_available_items',
        'user_id',
    ];

    protected $casts = [
        'working_days' => 'array',
        'working_hours' => 'array',
        'services' => 'array',
        'is_active' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function orderRentals(): HasMany
    {
        return $this->hasMany(OrderRental::class);
    }

    public function orderSales(): HasMany
    {
        return $this->hasMany(OrderSale::class);
    }
}
