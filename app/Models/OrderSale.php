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
        'status',
        'is_suspended',
        'is_including_lobes',
    ];

    protected $casts = [
        'is_suspended' => 'boolean',
    ];


    /**
     * Order Status Constants
     * حالات الطلب
     *
     * Flow:
     * - User adds piece for sale: pending_approval
     * - Store approves: approved
     * - Store confirms sending: piece_sent
     * - After sale: sold
     * - Store rejects: rejected
     */
    const STATUS_PENDING_APPROVAL = 'pending_approval'; // في انتظار المتجر
    const STATUS_APPROVED = 'approved'; // تم القبول من المتجر
    const STATUS_PIECE_SENT = 'piece_sent'; // تم الإرسال للعميل
    const STATUS_VENDOR_ALREADY_TAKE_THE_PIECE  = 'vendor_already_take_the_piece';
    const STATUS_SOLD = 'sold'; // تم بيعها
    const STATUS_REJECTED = 'rejected'; // تم رفضها
    const STATUS_CANCELED = 'canceled'; // تم الإلغاء
    const STATUS_CONFIRM_SOLD_FROM_VENDOR = 'confirm_sold_from_vendor'; // تم التأكيد من المتجر

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
            self::STATUS_CANCELED,
            self::STATUS_CONFIRM_SOLD_FROM_VENDOR,
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
    public function contacts()
    {
        return $this->hasMany(Contact::class,'sale_order_id');
    }
}