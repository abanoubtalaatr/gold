<?php

namespace App\Services;

use App\Events\RentalStatusUpdatedEvent;
use App\Models\OrderRental;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RealTimeNotificationService
{
    /**
     * Send critical notification immediately (no queue)
     */
    public function sendCriticalNotification(OrderRental $order, string $oldStatus, string $newStatus, $actor = null): void
    {
        try {
            $this->markUserOnline($order->user_id);
            
            // Broadcast immediately to Pusher
            $this->broadcastImmediately($order, $oldStatus, $newStatus, $actor, 'critical');
            
            // Save to database immediately
            $this->saveNotificationToDatabase($order, $oldStatus, $newStatus, $actor);
            
            Log::info('Critical notification sent immediately', [
                'order_id' => $order->id,
                'status_change' => "{$oldStatus} → {$newStatus}",
                'delivery_type' => 'immediate_critical',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send critical notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send sound notification immediately
     */
    public function sendSoundNotification(OrderRental $order, string $oldStatus, string $newStatus, $actor = null): void
    {
        try {
            $this->markUserOnline($order->user_id);
            
            // Broadcast immediately to Pusher
            $this->broadcastImmediately($order, $oldStatus, $newStatus, $actor, 'sound');
            
            // Save to database immediately
            $this->saveNotificationToDatabase($order, $oldStatus, $newStatus, $actor);
            
            Log::info('Sound notification sent immediately', [
                'order_id' => $order->id,
                'status_change' => "{$oldStatus} → {$newStatus}",
                'delivery_type' => 'immediate_sound',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send sound notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send standard rental status update immediately
     */
    public function sendRentalStatusUpdate(OrderRental $order, string $oldStatus, string $newStatus, $actor = null): void
    {
        try {
            $this->markUserOnline($order->user_id);
            
            // Broadcast immediately to Pusher
            $this->broadcastImmediately($order, $oldStatus, $newStatus, $actor, 'standard');
            
            // Save to database immediately
            $this->saveNotificationToDatabase($order, $oldStatus, $newStatus, $actor);
            
            Log::info('Standard notification sent immediately', [
                'order_id' => $order->id,
                'status_change' => "{$oldStatus} → {$newStatus}",
                'delivery_type' => 'immediate_standard',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send standard notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Broadcast notification immediately to Pusher with retry logic
     */
    private function broadcastImmediately(OrderRental $order, string $oldStatus, string $newStatus, $actor, string $type): void
    {
        $maxRetries = 3;
        $retryDelay = 500000; // 0.5 seconds in microseconds
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Load relationships
                $order->load(['user', 'branch.vendor', 'goldPiece']);
                
                // Create and broadcast event directly
                $event = new RentalStatusUpdatedEvent($order, $oldStatus, $newStatus, $actor);
                broadcast($event)->toOthers();
                
                Log::info("Direct broadcast successful", [
                    'order_id' => $order->id,
                    'attempt' => $attempt,
                    'type' => $type,
                    'user_id' => $order->user_id,
                    'channel' => "private-notifications.{$order->user_id}",
                ]);
                
                return; // Success, exit retry loop
                
            } catch (\Exception $e) {
                Log::warning("Direct broadcast attempt {$attempt} failed", [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                
                if ($attempt < $maxRetries) {
                    usleep($retryDelay); // Wait before retry
                } else {
                    // All retries failed
                    Log::error("All direct broadcast attempts failed", [
                        'order_id' => $order->id,
                        'total_attempts' => $maxRetries,
                    ]);
                    throw $e;
                }
            }
        }
    }

    /**
     * Save notification to database immediately (no queue)
     */
    private function saveNotificationToDatabase(OrderRental $order, string $oldStatus, string $newStatus, $actor): void
    {
        try {
            $order->load(['user', 'branch.vendor', 'goldPiece']);
            
            // Get all users to notify
            $notifiables = $this->getNotifiableUsers($order, $newStatus);
            
            foreach ($notifiables as $notifiable) {
                // Create notification data
                $notificationData = $this->createNotificationData($order, $oldStatus, $newStatus, $actor, $notifiable);
                
                // Save directly to database
                $notifiable->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\\Services\\RealTimeNotificationService',
                    'data' => $notificationData,
                    'read_at' => null,
                ]);
                
                Log::debug('Notification saved to database', [
                    'order_id' => $order->id,
                    'user_id' => $notifiable->id,
                    'status' => $newStatus,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to save notification to database', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw here, broadcasting is more important than database storage
        }
    }

    /**
     * Create notification data
     */
    private function createNotificationData(OrderRental $order, string $oldStatus, string $newStatus, $actor, $notifiable): array
    {
        $message = $this->getStatusMessage($order, $newStatus);
        
        return [
            'title' => [
                'ar' => 'تحديث حالة طلب التأجير',
                'en' => 'Rental Request Status Update'
            ],
            'message' => $message,
            'type' => 'rental_status_update',
            'priority' => $this->getNotificationPriority($newStatus),
            'sound_enabled' => $this->shouldPlaySound($newStatus),
            'data' => [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'gold_piece_id' => $order->gold_piece_id,
                'gold_piece_name' => $order->goldPiece->name,
                'user_id' => $order->user_id,
                'user_name' => $order->user->name,
                'branch_id' => $order->branch_id,
                'branch_name' => $order->branch?->name,
                'actor' => $actor ? [
                    'id' => $actor->id,
                    'name' => $actor->name,
                    'type' => class_basename($actor),
                ] : null,
                'action_url' => url("/api/v1/orders/{$order->id}"),
                'timestamp' => now()->toISOString(),
                'is_instant' => true,
                'requires_action' => $this->requiresUserAction($notifiable, $newStatus, $order),
            ]
        ];
    }

    /**
     * Get status message
     */
    private function getStatusMessage(OrderRental $order, string $newStatus): array
    {
        $userName = $order->user->name;
        $pieceName = $order->goldPiece->name;
        
        return match ($newStatus) {
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

    /**
     * Get notification priority
     */
    private function getNotificationPriority(string $newStatus): string
    {
        return match ($newStatus) {
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
     * Should play sound
     */
    private function shouldPlaySound(string $newStatus): bool
    {
        return in_array($newStatus, [
            OrderRental::STATUS_PENDING_APPROVAL,
            OrderRental::STATUS_APPROVED,
            OrderRental::STATUS_REJECTED,
            OrderRental::STATUS_PIECE_SENT,
            OrderRental::STATUS_RENTED,
            OrderRental::STATUS_AVAILABLE,
        ]);
    }

    /**
     * Check if notification requires user action
     */
    private function requiresUserAction($notifiable, string $newStatus, OrderRental $order): bool
    {
        if ($newStatus === OrderRental::STATUS_PENDING_APPROVAL && 
            $notifiable instanceof \App\Models\User && 
            $notifiable->hasRole('vendor')) {
            return true;
        }
        
        if ($newStatus === OrderRental::STATUS_PIECE_SENT && 
            $notifiable->id === $order->user_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Get users that should be notified
     */
    private function getNotifiableUsers(OrderRental $order, string $newStatus): array
    {
        $notifiables = [];

        // Determine who should be notified based on the status change
        switch ($newStatus) {
            case OrderRental::STATUS_PENDING_APPROVAL:
                // New rental request - notify vendor only
                if ($order->branch && $order->branch->vendor) {
                    $notifiables[] = $order->branch->vendor;
                }
                break;
                
            case OrderRental::STATUS_APPROVED:
            case OrderRental::STATUS_REJECTED:
            case OrderRental::STATUS_PIECE_SENT:
            case OrderRental::STATUS_RENTED:
            case OrderRental::STATUS_AVAILABLE:
                // Vendor actions - notify user only
                if ($order->user) {
                    $notifiables[] = $order->user;
                }
                break;
                
            default:
                // Fallback: notify the user
                if ($order->user) {
                    $notifiables[] = $order->user;
                }
                break;
        }

        // Always notify gold piece owner if different from requester (for rental completion)
        if ($newStatus === OrderRental::STATUS_AVAILABLE && 
            $order->type === OrderRental::RENT_TYPE && 
            $order->goldPiece && 
            $order->goldPiece->user && 
            $order->goldPiece->user->id !== $order->user_id) {
            $notifiables[] = $order->goldPiece->user;
        }

        return array_unique($notifiables, SORT_REGULAR);
    }

    /**
     * Mark user as online for activity tracking
     */
    public function markUserOnline(int $userId): void
    {
        Cache::put("user_online_{$userId}", true, 300); // 5 minutes
    }

    /**
     * Check if user is online
     */
    public function isUserOnline(int $userId): bool
    {
        return Cache::has("user_online_{$userId}");
    }
}