<?php

namespace App\Notifications\Vendor;

use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification
{
    use Queueable;

    /**
     * The contact instance.
     *
     * @var \App\Models\Contact
     */
    protected $contact;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Contact $contact
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
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
        // Prepare message preview
        $message = $this->contact->message;

        $preview = strlen($message) > 100 ? substr($message, 0, 60) . '...' : $message;

        return [
            'title' => [
                'ar' => 'رسالة جديدة من عميل',
                'en' => 'New Contact Message'
            ],
            'message' => [
                'ar' => "لديك رسالة جديدة من عميل {$preview}",
                'en' => "You have a new contact message: {$preview}"
            ],
            'type' => 'new_contact_message',
            'data' => [
                'contact_id' => $this->contact->id,
                'subject' => $this->contact->subject,
                'user_name' => $this->contact->user->name ?? null,
                'link' => route('vendor.contacts.index'),
            ],
        ];
    }
}