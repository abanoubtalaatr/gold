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
        'status'
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
     */
    
    /**
     * Pending store approval
     * في انتظار قبول المتجر
     */
    const STATUS_PENDING_APPROVAL = 'pending_approval';

    /**
     * Approved by store
     * تم القبول من المتجر
     */
    const STATUS_APPROVED = 'approved';

    /**
     * Piece sent to store
     * تم ارسال القطعة للمتجر
     */
    const STATUS_PIECE_SENT = 'piece_sent';

    /**
     * Available for rent
     * متاحة للتأجير
     */
    const STATUS_AVAILABLE = 'available';

    /**
     * Currently rented
     * مؤجرة
     */
    const STATUS_RENTED = 'rented';

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