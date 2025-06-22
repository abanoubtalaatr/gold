<?php

namespace App\Events\Vendor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $message;
    public $vendorId;
    /**
     * Create a new event instance.
     */
    public function __construct($title , $message , $vendorId)
    {
        
        $this->title = $title;
        $this->message = $message;
        $this->vendorId = $vendorId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        
        return [
            new Channel('vendor-notifications.'.$this->vendorId),
        ];
    }

    /**
     * Get the name of the event.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'vendor-notification';
    }
    public function broadcastWith()
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
