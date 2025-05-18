<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $fillable = [
        'module_name',
        'action',
        'badge',
        'affected_record_id',
        'original_data',
        'updated_data',
        'by_user_id',
    ];

    protected $casts = [
        'original_data' => 'json',
        'updated_data' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'by_user_id');
    }
} 