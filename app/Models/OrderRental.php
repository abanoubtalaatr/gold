<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gold_piece_id',
        'branch_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'type',
        'is_suspended',
    ];

    protected $casts = [
        'is_suspended' => 'boolean',
    ];


    /**
     * Order Types Constants
     * معني RENT_TYPE - المستخدم يضيف قطعة ذهبية لتأجيرها للمحل
     */
    const RENT_TYPE = 'rent';

    /**
     * معني LEASE_TYPE - المستخدم يطلب تأجير قطعة من المحل
     */
    const LEASE_TYPE = 'lease';

    /**
     * Order Status Constants
     * حالات الطلب
     *
     * Flow:
     * - User adds piece for rent: pending_approval
     * - Store approves: approved
     * - Store rejects: rejected
     * - Piece sent to store: piece_sent
     * - Store approves rental: rented
     * - After rental ends: available
     */
    const STATUS_PENDING_APPROVAL = 'pending_approval'; // في انتظار المتجر
    const STATUS_APPROVED = 'approved'; // تم القبول من المتجر
    const STATUS_PIECE_SENT = 'piece_sent'; // تم ارسال القطعة للمتجر
    const STATUS_RENTED = 'rented'; // مؤجرة حاليا
    const STATUS_AVAILABLE = 'available'; // متاحة للإيجار مرة أخرى
    const STATUS_SOLD = 'sold'; // تم بيعها (if needed for rental orders)
    const STATUS_REJECTED = 'rejected'; // تم رفضها


    /**
     * Get all possible order statuses.
     *
     * @return array
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING_APPROVAL,  // في انتظار المتجر
            self::STATUS_APPROVED,          // تم القبول من المتجر
            self::STATUS_PIECE_SENT,        // تم ارسال القطعة للمتجر
            self::STATUS_RENTED,            // مؤجرة حاليا
            self::STATUS_AVAILABLE,         // متاحة للإيجار مرة أخرى
            self::STATUS_SOLD,              // تم بيعها
            self::STATUS_REJECTED,          // تم رفضها
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
        return $this->hasMany(Contact::class,'rental_order_id');
    }
}