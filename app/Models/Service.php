<?php

namespace App\Models;

use App\Models\ServiceImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'type',
        'name',
        'description',
        'price',
        'available_sessions_per_day',
        'duration',
        'max_concurrent_requests',
        'location_type',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Service Types
    public const TYPE_CUPPING = 'cupping';
    public const TYPE_MASSAGE = 'massage';

    public const TYPES = [
        self::TYPE_CUPPING,
        self::TYPE_MASSAGE,
    ];

    // Location Types
    public const LOCATION_HOME = 'home';
    public const LOCATION_CENTER = 'center';
    public const LOCATION_BOTH = 'both';

    public const LOCATIONS = [
        self::LOCATION_HOME,
        self::LOCATION_CENTER,
        self::LOCATION_BOTH,
    ];

    // Duration options in minutes
    public const DURATIONS = [
        30, 45, 60, 90, 120
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeAvailableAt($query, string $locationType)
    {
        return $query->where(function ($q) use ($locationType) {
            $q->where('location_type', $locationType)
                ->orWhere('location_type', self::LOCATION_BOTH);
        });
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' SAR';
    }

    public function getFormattedDurationAttribute(): string
    {
        return $this->duration . ' ' . trans_choice('common.minutes', $this->duration);
    }

    public function getLocationTextAttribute(): string
    {
        return match($this->location_type) {
            self::LOCATION_HOME => __('services.location_home'),
            self::LOCATION_CENTER => __('services.location_center'),
            self::LOCATION_BOTH => __('services.location_both'),
            default => ''
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CUPPING => __('services.type_cupping'),
            self::TYPE_MASSAGE => __('services.type_massage'),
            default => ''
        };
    }

    // Helper methods
    public function hasActiveBookings(): bool
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->exists();
    }

    public function isAvailableAt(string $locationType): bool
    {
        return $this->location_type === $locationType || 
               $this->location_type === self::LOCATION_BOTH;
    }
} 