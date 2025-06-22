<?php

namespace App\Events\Admin;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $message;
    public $adminId;
    /**
     * Create a new event instance.
     */
    public function __construct($title , $message , $adminId)
    {
        
        $this->title = $title;
        $this->message = $message;
        $this->adminId = $adminId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        
        return [
            new Channel('admin-notifications.'.$this->adminId),
        ];
    }

    /**
     * Get the name of the event.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'admin-notification';
    }
    public function broadcastWith()
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
}
