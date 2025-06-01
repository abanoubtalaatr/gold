<?php

namespace App\Notifications\Admin;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewContactMessageAdmin extends Notification
{
    use Queueable;

    protected $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $message = $this->contact->message;
        $preview = strlen($message) > 100 ? substr($message, 0, 60) . '...' : $message;

        return [
            'title' => [
                'ar' => 'شكوى جديدة من عميل',
                'en' => 'New Contact Message'
            ],
            'message' => [
                'ar' => "شكوى جديدة من {$this->contact->user->name}: {$preview}",
                'en' => "New message from {$this->contact->user->name}: {$preview}"
            ],
            'type' => 'new_contact_message_admin',
            'data' => [
                'contact_id' => $this->contact->id,
                'subject' => $this->contact->subject,
                'user_name' => $this->contact->user->name ?? null,
                // 'link' => route('admin.contacts.show', $this->contact->id),
            ],
        ];
    }
}
