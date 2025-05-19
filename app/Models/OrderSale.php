<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'gold_piece_id', 
        'branch_id',
        'total_price', 
        'status'
    ];

    /**
     * Order Status Constants
     * حالات الطلب
     *
     * Flow:
     * - User adds piece for sale: pending_approval
     * - Store approves: approved
     * - After sale: sold
     */
    const STATUS_PENDING_APPROVAL = 'pending_approval'; // في انتظار المتجر
    const STATUS_APPROVED = 'approved'; // تم القبول من المتجر
    const STATUS_SOLD = 'sold'; // تم بيعها

    /**
     * Get all possible order statuses.
     *
     * @return array
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_APPROVED,
            self::STATUS_SOLD,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goldPiece(): BelongsTo
    {
        return $this->belongsTo(GoldPiece::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}