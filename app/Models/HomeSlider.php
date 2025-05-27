<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    protected $fillable = [
        'image_path',
        'display_from',
        'display_to',
        'display_order',
        'is_active'
    ];

    protected $dates = [
        'display_from',
        'display_to'
    ];
}
