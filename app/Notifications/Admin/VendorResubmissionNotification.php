<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VendorResubmissionNotification extends Notification
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
                'ar' => 'إعادة تقديم طلب مراجعة متجر',
                'en' => 'Vendor Store Resubmission Request'
            ],
            'message' => [
                'ar' => "قام البائع {$this->vendor->store_name_ar} بإعادة تقديم معلومات المتجر للمراجعة مرة أخرى",
                'en' => "Vendor {$this->vendor->store_name_en} has resubmitted store information for review"
            ],
            'type' => 'vendor_store_resubmission',
            'priority' => 'medium',
            'sound_enabled' => true,
            'data' => [
                'vendor_id' => $this->vendor->id,
                'vendor_name_en' => $this->vendor->store_name_en,
                'vendor_name_ar' => $this->vendor->store_name_ar,
                'link' => '/admin/vendors/' . $this->vendor->id,
                'resubmitted_at' => now()->toISOString(),
            ],
        ];
    }
} 