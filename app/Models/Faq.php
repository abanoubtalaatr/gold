<?php

namespace App\Models;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model implements TranslatableContract
{
    use Translatable;


    public $translatedAttributes = ['question', 'answer'];

          protected $fillable = [

        'is_active',
        'type',

    ];

    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopActive($query){
        return $query->where('is_active', true);
    }
}