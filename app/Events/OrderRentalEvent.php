<?php

namespace App\Events;

use App\Models\OrderRental;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderRentalEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var OrderRental
     */
    public $orderRental;

    /**
     * @var int
     */
    public $branchId;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderRental $orderRental, int $branchId)
    {
        $this->orderRental = $orderRental;
        $this->branchId = $branchId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('branch.' . $this->branchId),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
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
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.rental';
    }
} 