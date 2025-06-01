<?php

namespace App\Events;

use App\Models\OrderRental;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RentalStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderRental;
    public $oldStatus;
    public $newStatus;
    public $actor; // vendor or user who performed the action

    public function __construct(OrderRental $orderRental, string $oldStatus, string $newStatus, $actor)
    {
        $this->orderRental = $orderRental->load(['user', 'goldPiece', 'branch']);
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->actor = $actor;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Only send to the user who made the order
        return [
            new PrivateChannel('notifications.' . $this->orderRental->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $message = $this->getStatusMessage();
        
        return [
            'id' => $this->orderRental->id,
            'order_id' => $this->orderRental->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $message,
            'user' => [
                'id' => $this->orderRental->user->id,
                'name' => $this->orderRental->user->name,
            ],
            'gold_piece' => [
                'id' => $this->orderRental->goldPiece->id,
                'name' => $this->orderRental->goldPiece->name,
            ],
            'branch' => [
                'id' => $this->orderRental->branch->id,
                'name' => $this->orderRental->branch->name,
            ],
            'actor' => $this->actor ? [
                'id' => $this->actor->id,
                'name' => $this->actor->name,
                'type' => class_basename($this->actor),
            ] : null,
            'timestamp' => now()->toISOString(),
            'sound_enabled' => true,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'rental.status.updated';
    }

    private function getStatusMessage(): array
    {
        $userName = $this->orderRental->user->name;
        $pieceName = $this->orderRental->goldPiece->name;
        
        return match ($this->newStatus) {
            'approved' => [
                'ar' => "تم قبول طلب تأجير {$pieceName} من المستخدم {$userName}",
                'en' => "Rental request for {$pieceName} from {$userName} has been approved"
            ],
            'rejected' => [
                'ar' => "تم رفض طلب تأجير {$pieceName} من المستخدم {$userName}",
                'en' => "Rental request for {$pieceName} from {$userName} has been rejected"
            ],
            'piece_sent' => [
                'ar' => "تم إرسال القطعة {$pieceName} إلى المستخدم {$userName}",
                'en' => "Piece {$pieceName} has been sent to user {$userName}"
            ],
            'rented' => [
                'ar' => "تم تأجير القطعة {$pieceName} بنجاح للمستخدم {$userName}",
                'en' => "Piece {$pieceName} is now successfully rented to {$userName}"
            ],
            'available' => [
                'ar' => "انتهى تأجير القطعة {$pieceName} وأصبحت متاحة مرة أخرى",
                'en' => "Rental period for {$pieceName} has ended and it's now available again"
            ],
            default => [
                'ar' => "تم تحديث حالة طلب التأجير",
                'en' => "Rental request status has been updated"
            ]
        };
    }
} 