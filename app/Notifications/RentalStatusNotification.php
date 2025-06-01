<?php

namespace App\Notifications;

use App\Models\OrderRental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class RentalStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $orderRental;
    protected $oldStatus;
    protected $newStatus;
    protected $actor;
    protected $isInstant;

    public function __construct(OrderRental $orderRental, string $oldStatus, string $newStatus, $actor = null, bool $isInstant = false)
    {
        $this->orderRental = $orderRental;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->actor = $actor;
        $this->isInstant = $isInstant;
        
        // Set queue properties for better real-time experience
        $this->onQueue($this->isInstant ? 'instant' : 'high');
        $this->delay($this->isInstant ? 0 : 2); // Instant or 2 seconds delay
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];
        
        // Add additional channels for critical status changes
        if ($this->isCriticalStatus()) {
            // Could add push notifications, SMS, etc. in the future
        }
        
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $message = $this->getStatusMessage();
        
        return [
            'title' => [
                'ar' => 'تحديث حالة طلب التأجير',
                'en' => 'Rental Request Status Update'
            ],
            'message' => $message,
            'type' => 'rental_status_update',
            'priority' => $this->getNotificationPriority(),
            'sound_enabled' => $this->shouldPlaySound(),
            'data' => [
                'order_id' => $this->orderRental->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'gold_piece_id' => $this->orderRental->gold_piece_id,
                'gold_piece_name' => $this->orderRental->goldPiece->name,
                'user_id' => $this->orderRental->user_id,
                'user_name' => $this->orderRental->user->name,
                'branch_id' => $this->orderRental->branch_id,
                'branch_name' => $this->orderRental->branch->name,
                'actor' => $this->actor ? [
                    'id' => $this->actor->id,
                    'name' => $this->actor->name,
                    'type' => class_basename($this->actor),
                ] : null,
                'action_url' => $this->getActionUrl($notifiable),
                'timestamp' => now()->toISOString(),
                'is_instant' => $this->isInstant,
                'requires_action' => $this->requiresUserAction($notifiable),
            ]
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $data = $this->toDatabase($notifiable);
        
        // Add real-time specific data
        $data['broadcast_timestamp'] = now()->toISOString();
        $data['notification_id'] = $this->id;
        
        return new BroadcastMessage($data);
    }

    /**
     * Determine if notification should play sound
     */
    private function shouldPlaySound(): bool
    {
        return in_array($this->newStatus, [
            OrderRental::STATUS_PENDING_APPROVAL, // New rental request
            OrderRental::STATUS_APPROVED,         // Request approved
            OrderRental::STATUS_REJECTED,         // Request rejected
            OrderRental::STATUS_PIECE_SENT,       // Piece sent
            OrderRental::STATUS_RENTED,           // Rental confirmed
            OrderRental::STATUS_AVAILABLE,        // Rental completed
        ]);
    }

    /**
     * Get notification priority level
     */
    private function getNotificationPriority(): string
    {
        return match ($this->newStatus) {
            OrderRental::STATUS_PENDING_APPROVAL,
            OrderRental::STATUS_APPROVED,
            OrderRental::STATUS_REJECTED => 'high',
            OrderRental::STATUS_PIECE_SENT,
            OrderRental::STATUS_RENTED => 'medium',
            OrderRental::STATUS_AVAILABLE => 'normal',
            default => 'normal'
        };
    }

    /**
     * Check if this is a critical status that needs immediate attention
     */
    private function isCriticalStatus(): bool
    {
        return in_array($this->newStatus, [
            OrderRental::STATUS_PENDING_APPROVAL,
            OrderRental::STATUS_APPROVED,
            OrderRental::STATUS_REJECTED,
        ]);
    }

    /**
     * Check if notification requires user action
     */
    private function requiresUserAction(object $notifiable): bool
    {
        // Vendors need to act on pending approvals
        if ($this->newStatus === OrderRental::STATUS_PENDING_APPROVAL && 
            $notifiable instanceof \App\Models\User && 
            $notifiable->hasRole('vendor')) {
            return true;
        }
        
        // Users need to confirm when piece is sent
        if ($this->newStatus === OrderRental::STATUS_PIECE_SENT && 
            $notifiable->id === $this->orderRental->user_id) {
            return true;
        }
        
        return false;
    }

    private function getStatusMessage(): array
    {
        $userName = $this->orderRental->user->name;
        $pieceName = $this->orderRental->goldPiece->name;
        
        return match ($this->newStatus) {
            OrderRental::STATUS_APPROVED => [
                'ar' => "تم قبول طلب تأجير {$pieceName}",
                'en' => "Your rental request for {$pieceName} has been approved"
            ],
            OrderRental::STATUS_REJECTED => [
                'ar' => "تم رفض طلب تأجير {$pieceName}",
                'en' => "Your rental request for {$pieceName} has been rejected"
            ],
            OrderRental::STATUS_PIECE_SENT => [
                'ar' => "تم إرسال القطعة {$pieceName} إليك",
                'en' => "The piece {$pieceName} has been sent to you"
            ],
            OrderRental::STATUS_RENTED => [
                'ar' => "تم تأجير القطعة {$pieceName} بنجاح",
                'en' => "The piece {$pieceName} is now successfully rented"
            ],
            OrderRental::STATUS_AVAILABLE => [
                'ar' => "انتهى تأجير القطعة {$pieceName} وأصبحت متاحة مرة أخرى",
                'en' => "Rental period for {$pieceName} has ended and is now available"
            ],
            OrderRental::STATUS_PENDING_APPROVAL => [
                'ar' => "طلب تأجير جديد للقطعة {$pieceName} من المستخدم {$userName}",
                'en' => "New rental request for {$pieceName} from {$userName}"
            ],
            default => [
                'ar' => "تم تحديث حالة طلب التأجير",
                'en' => "Rental request status has been updated"
            ]
        };
    }

    private function getActionUrl($notifiable): string
    {
        // Different URLs based on notifiable type and status
        if ($notifiable instanceof \App\Models\User) {
            if ($notifiable->hasRole('vendor')) {
                // For vendors - try to use vendor route, fallback to URL
                try {
                    return route('vendor.rental-requests.index');
                } catch (\Exception $e) {
                    return url('/vendor/rental-orders');
                }
            } else {
                // For regular users - use direct URL for mobile app
                return url("/api/v1/orders/{$this->orderRental->id}");
            }
        }
        
        return url("/api/v1/orders/{$this->orderRental->id}");
    }

    /**
     * Create an instant notification (bypasses queue)
     */
    public static function instant(OrderRental $orderRental, string $oldStatus, string $newStatus, $actor = null): self
    {
        return new self($orderRental, $oldStatus, $newStatus, $actor, true);
    }

    /**
     * Get the channels the notification should broadcast on.
     */
    public function broadcastOn()
    {
        $channels = [];
        
        // Always broadcast to user
        if ($this->orderRental->user_id) {
            $channels[] = "notifications.{$this->orderRental->user_id}";
        }
        
        // Broadcast to branch if exists
        if ($this->orderRental->branch_id) {
            $channels[] = "branch.{$this->orderRental->branch_id}";
        }
        
        // Broadcast to vendor if exists
        if ($this->orderRental->branch && $this->orderRental->branch->vendor_id) {
            $channels[] = "vendor.{$this->orderRental->branch->vendor_id}";
        }
        
        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'rental.notification';
    }
} 