<?php

namespace App\Services;

use App\Events\RentalStatusUpdatedEvent;
use App\Models\OrderRental;
use App\Services\RealTimeNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RentalWorkflowService
{
    protected $realTimeNotificationService;

    public function __construct(RealTimeNotificationService $realTimeNotificationService)
    {
        $this->realTimeNotificationService = $realTimeNotificationService;
    }

    /**
     * Update rental status with notifications and events
     */
    public function updateStatus(OrderRental $order, string $newStatus, $actor = null): bool
    {
        $oldStatus = $order->status;
        
        if ($oldStatus === $newStatus) {
            return true; // No change needed
        }

        try {
            DB::beginTransaction();

            // Update the order status
            $order->update(['status' => $newStatus]);
            
            // Handle special cases for automatic status progression
            $this->handleStatusTransition($order, $oldStatus, $newStatus);

            // Send enhanced real-time notifications
            $this->sendRealTimeNotifications($order, $oldStatus, $newStatus, $actor);

            DB::commit();

            Log::info('Rental status updated successfully', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'actor_id' => $actor?->id,
                'actor_type' => $actor ? class_basename($actor) : null,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update rental status', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Approve a rental request with instant notification
     */
    public function approve(OrderRental $order , $actor): bool
    {
        
        // Use critical notification for approval
        $oldStatus = $order->status;
        $order->update(['status' => OrderRental::STATUS_APPROVED]);
        
        $this->handleStatusTransition($order, $oldStatus, OrderRental::STATUS_APPROVED);
        $this->realTimeNotificationService->sendCriticalNotification($order, $oldStatus, OrderRental::STATUS_APPROVED, $actor);
        
        return true;
    }

    /**
     * Reject a rental request with instant notification
     */
    public function reject(OrderRental $order, $actor): bool
    {
        $oldStatus = $order->status;
        $order->update(['status' => OrderRental::STATUS_REJECTED]);
        
        // Use critical notification for rejection
        $this->realTimeNotificationService->sendCriticalNotification($order, $oldStatus, OrderRental::STATUS_REJECTED, $actor);
        
        return true;
    }

    /**
     * Mark piece as sent to user with sound notification
     */
    public function markAsSent(OrderRental $order, $actor): bool
    {
        $oldStatus = $order->status;
        $order->update(['status' => OrderRental::STATUS_PIECE_SENT]);
        
        // Use sound notification for piece sent
        $this->realTimeNotificationService->sendSoundNotification($order, $oldStatus, OrderRental::STATUS_PIECE_SENT, $actor);
        
        return true;
    }

    /**
     * Confirm rental start (when user receives the piece) with instant notification
     */
    public function confirmRental(OrderRental $order, $actor): bool
    {
        $oldStatus = $order->status;
        $order->update(['status' => OrderRental::STATUS_RENTED]);
        
        $this->handleStatusTransition($order, $oldStatus, OrderRental::STATUS_RENTED);
        $this->realTimeNotificationService->sendRentalStatusUpdate($order, $oldStatus, OrderRental::STATUS_RENTED, $actor);
        
        return true;
    }

    /**
     * Mark rental as completed/available again with enhanced real-time notification
     */
    public function completeRental(OrderRental $order, $actor = null): bool
    {
        $oldStatus = $order->status;
        $order->update(['status' => OrderRental::STATUS_AVAILABLE]);
        
        $this->handleStatusTransition($order, $oldStatus, OrderRental::STATUS_AVAILABLE);
        
        // Use critical notification for rental completion as it's important for all parties
        $this->realTimeNotificationService->sendCriticalNotification($order, $oldStatus, OrderRental::STATUS_AVAILABLE, $actor);
        
        return true;
    }

    /**
     * Handle special logic for status transitions
     */
    private function handleStatusTransition(OrderRental $order, string $oldStatus, string $newStatus): void
    {
        switch ($newStatus) {
            case OrderRental::STATUS_APPROVED:
                // When approved, set start date if not already set
                if (!$order->start_date) {
                    $order->update(['start_date' => now()->addDay()]);
                }
                break;

            case OrderRental::STATUS_RENTED:
                // When rental starts, update the actual start date
                $order->update(['start_date' => now()]);
                break;

            case OrderRental::STATUS_AVAILABLE:
                // When rental ends, the piece becomes available again
                // Update gold piece status if needed
                if ($order->goldPiece) {
                    $order->goldPiece->update(['status' => 'available']);
                }
                break;
        }
    }

    /**
     * Send enhanced real-time notifications based on status importance
     */
    private function sendRealTimeNotifications(OrderRental $order, string $oldStatus, string $newStatus, $actor): void
    {
        try {
            // Determine notification type based on status
            switch ($newStatus) {
                case OrderRental::STATUS_PENDING_APPROVAL:
                case OrderRental::STATUS_APPROVED:
                case OrderRental::STATUS_REJECTED:
                case OrderRental::STATUS_AVAILABLE:
                    // Critical notifications for important status changes
                    $this->realTimeNotificationService->sendCriticalNotification($order, $oldStatus, $newStatus, $actor);
                    break;
                    
                case OrderRental::STATUS_PIECE_SENT:
                    // Sound notification for piece sent
                    $this->realTimeNotificationService->sendSoundNotification($order, $oldStatus, $newStatus, $actor);
                    break;
                    
                case OrderRental::STATUS_RENTED:
                    // Regular real-time notification for rental confirmation
                    $this->realTimeNotificationService->sendRentalStatusUpdate($order, $oldStatus, $newStatus, $actor);
                    break;
                    
                default:
                    // Fallback to regular notification
                    $this->realTimeNotificationService->sendRentalStatusUpdate($order, $oldStatus, $newStatus, $actor);
                    break;
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to send enhanced real-time notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            // Fallback to basic notifications
            $this->sendBasicNotifications($order, $oldStatus, $newStatus, $actor);
        }
    }

    /**
     * Fallback basic notification method
     */
    private function sendBasicNotifications(OrderRental $order, string $oldStatus, string $newStatus, $actor): void
    {
        try {
            // Just log that we fell back to basic notifications
            // The real-time notification service already handles everything
            Log::warning('Falling back to basic notifications - real-time service failed', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
            
            // Try one more time with the real-time service
            $this->realTimeNotificationService->sendRentalStatusUpdate($order, $oldStatus, $newStatus, $actor);
            
        } catch (\Exception $e) {
            Log::error('Even basic notifications failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw here - we don't want to break the entire workflow
        }
    }

    /**
     * Get all users that should be notified for this status change
     */
    private function getNotifiableUsers(OrderRental $order, string $newStatus): array
    {
        $notifiables = [];

        // Always notify the user (rental requester)
        if ($order->user) {
            $notifiables[] = $order->user;
        }

        // Notify vendor based on the status change
        if ($order->branch && $order->branch->vendor) {
            $vendor = $order->branch->vendor;
            
            // Notify vendor for important status changes
            if (in_array($newStatus, [
                OrderRental::STATUS_PENDING_APPROVAL,
                OrderRental::STATUS_PIECE_SENT,
                OrderRental::STATUS_RENTED,
                OrderRental::STATUS_AVAILABLE
            ])) {
                $notifiables[] = $vendor;
            }
        }

        // Notify gold piece owner if different from requester (for RENT_TYPE)
        if ($order->type === OrderRental::RENT_TYPE && $order->goldPiece && $order->goldPiece->user) {
            $goldPieceOwner = $order->goldPiece->user;
            if ($goldPieceOwner->id !== $order->user_id) {
                $notifiables[] = $goldPieceOwner;
            }
        }

        return array_unique($notifiables, SORT_REGULAR);
    }

    /**
     * Get allowed status transitions for a given current status
     */
    public function getAllowedTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            OrderRental::STATUS_PENDING_APPROVAL => [
                OrderRental::STATUS_APPROVED,
                OrderRental::STATUS_REJECTED
            ],
            OrderRental::STATUS_APPROVED => [
                OrderRental::STATUS_PIECE_SENT,
                OrderRental::STATUS_REJECTED
            ],
            OrderRental::STATUS_PIECE_SENT => [
                OrderRental::STATUS_RENTED,
                OrderRental::STATUS_AVAILABLE // if something goes wrong
            ],
            OrderRental::STATUS_RENTED => [
                OrderRental::STATUS_AVAILABLE
            ],
            default => []
        };
    }

    /**
     * Check if a status transition is valid
     */
    public function isValidTransition(string $from, string $to): bool
    {
        $allowedTransitions = $this->getAllowedTransitions($from);
        return in_array($to, $allowedTransitions);
    }

    /**
     * Send instant notification (for critical updates that need immediate delivery)
     */
    public function sendInstantNotification(OrderRental $order, string $oldStatus, string $newStatus, $actor = null): void
    {
        try {
            $order->load(['user', 'branch.vendor', 'goldPiece']);
            
            // Use the real-time notification service for instant notifications
            $this->realTimeNotificationService->sendCriticalNotification($order, $oldStatus, $newStatus, $actor);
            
        } catch (\Exception $e) {
            Log::error('Failed to send instant notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
} 