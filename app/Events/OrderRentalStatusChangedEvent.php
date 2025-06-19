<?php

namespace App\Events;

use App\Http\Resources\Api\OrderRentalResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Models\OrderRental;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderSaleStatusChangedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public OrderRental $order)
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
            'order' => OrderRentalResource::make($this->order),
            'message_ar' => 'تم تحديث حالة الطلب',
            'message_en' => 'Order status has been updated',
            'title_ar' => 'تحديث حالة الطلب',
            'title_en' => 'Order status updated',
            'description_ar' => 'تم تحديث حالة الطلب',
            'description_en' => 'Order status has been updated',
        ];
    }
}
