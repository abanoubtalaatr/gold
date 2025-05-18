<?php

namespace App\Notifications;

use App\Models\OrderRental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OrderRentalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var OrderRental
     */
    private $orderRental;

    /**
     * Create a new notification instance.
     */
    public function __construct(OrderRental $orderRental)
    {
        $this->orderRental = $orderRental;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->orderRental->id,
            'user_id' => $this->orderRental->user_id,
            'gold_piece_id' => $this->orderRental->gold_piece_id,
            'type' => $this->orderRental->type,
            'status' => $this->orderRental->status,
            'message' => __('notifications.new_rental_request', [
                'user' => $this->orderRental->user->name,
                'piece' => $this->orderRental->goldPiece->name
            ])
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
} 