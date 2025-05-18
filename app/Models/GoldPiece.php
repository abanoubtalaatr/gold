<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GoldPiece extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name', 'weight', 'carat', 'rental_price_per_day', 'sale_price', 'deposit_amount',
        'branch_id', 'user_id', 'type', 'status', 'qr_code', 'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderRentals()
    {
        return $this->hasMany(OrderRental::class);
    }

    public function orderSales()
    {
        return $this->hasMany(OrderSale::class);
    }

    /**
     * Get the users who have favorited this gold piece.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    /**
     * Get the favorites for this gold piece.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the average rating for this gold piece.
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->ratings()->avg('rating') ?? 0.0;
    }

    /**
     * Get the total number of ratings for this gold piece.
     */
    public function getRatingCountAttribute(): int
    {
        return $this->ratings()->count();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->useDisk('public')
            ->onlyKeepLatest(5); // Keep only the latest 5 images
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->nonQueued();
    }
}