<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class PageSection extends Model implements TranslatableContract
{
    use Translatable;

    protected $fillable = ['page_id', 'image', 'type'];
    protected $appends = ['image_url'];

    public $translatedAttributes = ['title', 'description'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }



    public function getImageUrlAttribute()
    {
        return asset("storage/{$this->attributes['image']}");
    }
}