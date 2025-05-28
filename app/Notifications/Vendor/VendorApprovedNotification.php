<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VendorApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
                'ar' => 'تم الموافقة على حساب المتجر',
                'en' => 'Vendor Account Approved'
            ],
            'message' => [
                'ar' => 'تمت الموافقة على حساب المتجر الخاص بك. يمكنك الآن البدء في إدارة متجرك.',
                'en' => 'Your vendor account has been approved. You can now start managing your store.'
            ],
            'type' => 'vendor_approved',
            'data' => [
                'link' => '/vendor/dashboard',
            ],
        ];
    }
}