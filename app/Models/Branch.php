<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;

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


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }
}
