<?php

namespace App\Events;

use App\Models\OrderSale;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Api\OrderSaleResource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderSaleStatusChangedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public OrderSale $order)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {   
        return new Channel('order-status-changed.' . $this->order->user_id);
    }

    public function broadcastWith()
    {
        return [
            'order' => OrderSaleResource::make($this->order),
            'message_ar' => 'تم تحديث حالة الطلب',
            'message_en' => 'Order status has been updated',
        ];
    }
}
