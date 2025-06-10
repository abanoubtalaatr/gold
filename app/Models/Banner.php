<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Banner extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $fillable = [
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public $translatedAttributes = ['title', 'description'];


    public function getImageUrlAttribute()
    {
        if ($this->attributes['image']) {
            return asset('storage/' . $this->attributes['image']);
        }
        return null;
    }

    public function getImageAttribute()
    {
        return $this->attributes['image'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}