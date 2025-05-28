<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VendorRejectedNotification extends Notification
{
    use Queueable;

    /**
     * The rejection reason.
     *
     * @var string
     */
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @param string $reason
     */
    public function __construct($reason)
    {
        $this->reason = $reason;
    }

    /**
     * Get the notification channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => [
                'ar' => 'تم رفض طلب الانضمام للمنصة',
                'en' => 'Vendor Application Rejected'
            ],
            'message' => [
                'ar' => 'تم رفض طلب الانضمام الخاص بك. السبب: ' . $this->reason,
                'en' => 'Your vendor application has been rejected. Reason: ' . $this->reason
            ],
            'type' => 'vendor_rejected',
            'data' => [
                'reason' => $this->reason,
                'link' => '/vendor/application',
            ],
        ];
    }
}
