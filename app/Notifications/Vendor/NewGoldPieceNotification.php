<?php

namespace App\Notifications\Vendor;

use App\Models\GoldPiece;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGoldPieceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $notificationData;

    public function __construct(array $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'title' => $this->notificationData['title'],
            'message ' => $this->notificationData['message'],
            'type' => $this->notificationData['type'],
            'data' => $this->notificationData['data'] ?? null,
        ]);
    }


}