<?php

namespace App\Models;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    protected $fillable = [
        'faq_id',
        'locale',
        'question',
        'answer',
    ];


}
