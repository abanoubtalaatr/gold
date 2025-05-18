<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiquidationRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'amount',
        'bank_account_name',
        'bank_account_number',
        'bank_account_swift',
        'bank_account_iban',
        'bank_account_holder_name',
    ];

    /**
     * Get the user that owns the liquidation request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
