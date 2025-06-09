<?php

namespace App\Events;

use App\Models\OrderRental;
use App\Models\OrderSale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderForVendorEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $vendorId;
    public $orderType;
    public $soundEnabled;

    public function __construct($order, int $vendorId, string $orderType = 'rental')
    {
        $this->order = $order->load(['user', 'goldPiece', 'branch']);
        $this->vendorId = $vendorId;
        $this->orderType = $orderType;
        $this->soundEnabled = true; // Always enable sound for new orders
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendor.' . $this->vendorId),
            new PrivateChannel('vendor.notifications.' . $this->vendorId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $goldPiece = $this->order->goldPiece;
        $customer = $this->order->user;
        $branch = $this->order->branch;

        return [
            'order_id' => $this->order->id,
            'order_type' => $this->orderType,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'gold_piece' => [
                'id' => $goldPiece->id,
                'name' => $goldPiece->name,
                'weight' => $goldPiece->weight,
                'carat' => $goldPiece->carat,
                'type' => $goldPiece->type,
            ],
            'branch' => [
                'id' => $branch->id,
                'name' => $branch->name,
            ],
            'price' => $this->orderType === 'rental' 
                ? $goldPiece->rental_price_per_day 
                : $goldPiece->sale_price,
            'message' => [
                'ar' => $this->orderType === 'rental' 
                    ? "طلب تأجير جديد للقطعة {$goldPiece->name} من العميل {$customer->name} في فرع {$branch->name}"
                    : "طلب شراء جديد للقطعة {$goldPiece->name} من العميل {$customer->name} في فرع {$branch->name}",
                'en' => $this->orderType === 'rental'
                    ? "New rental request for {$goldPiece->name} from customer {$customer->name} at {$branch->name} branch"
                    : "New purchase request for {$goldPiece->name} from customer {$customer->name} at {$branch->name} branch"
            ],
            'sound_enabled' => $this->soundEnabled,
            'priority' => 'high',
            'timestamp' => now()->toISOString(),
            'action_url' => $this->orderType === 'rental' 
                ? route('vendor.orders.rental.index') 
                : route('vendor.orders.index'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new.order.for.vendor';
    }
} 