<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'name',
        'latitude',
        'longitude',
        'address',
        'working_days',
        'working_hours',
        'services',
        'is_active',
        'city_id',
    ];

    protected $casts = [
        'working_days' => 'array',
        'working_hours' => 'array',
        'services' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class,'vendor_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
} 
