<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\User;

class NewVendorRegistrationNotification extends Notification
{
    use Queueable;

    protected $vendor;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => [
                'ar' => 'طلب انضمام بائع جديد',
                'en' => 'New Vendor Registration Request'
            ],
            'message' => [
                'ar' => "طلب انضمام جديد من البائع: {$this->vendor->store_name_ar}",
                'en' => "New registration request from vendor: {$this->vendor->store_name_en}"
            ],
            'type' => 'new_vendor_registration',
            'data' => [
                'vendor_id' => $this->vendor->id,
                // 'action_url' => route('admin.vendors.show', $this->vendor->id)
            ]
        ];
    }
}
