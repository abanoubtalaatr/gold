<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorRejectedNotification extends Notification 
{

    protected $reason;

    public function __construct($reason)
    {
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return [ 'database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Vendor Application Rejected',
            'message' => 'Your vendor application has been rejected. Reason: ' . $this->reason,
            'url' => '/vendor/application',
        ];
    }
}