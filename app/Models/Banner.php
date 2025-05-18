<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Banner extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = ['is_active', 'image', 'sort_order'];

    public $translatedAttributes = ['title', 'description'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return asset("storage/{$this->attributes['image']}");
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}