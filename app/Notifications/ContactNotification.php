<?php

namespace App\Notifications;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Http\Requests\Api\ContactUsRequest;
use Illuminate\Notifications\Messages\MailMessage;

class ContactNotification extends Notification
{
    use Queueable;

    /**
     * @var $name
     */
    public $name;

    public $model_id;

    /**
     * @var $phone
     */
    public $phone;

    /**
     * @var $email
     */
    public $email;

    /**
     * @var $message
     */
    public $message;

    public $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data, $model_id)
    {
        $this->model_id = $model_id;
        $this->name = $data['name'];
        $this->phone = $data['phone'];
        $this->email = $data['email'];
        $this->message = $data['message'];
        $this->subject = $data['subject'] ?? null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = [
            'model_id' => $this->model_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'message' => $this->message,
            'subject' => $this->subject,
        ];

        return (new MailMessage)
            ->markdown("emails.contact-request", $data);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        return [
            'model_id' => $this->model_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'message' => $this->message,
            'subject' => $this->subject,
            'ar' => [
                'body' => 'رسالة تواصل جديدة من ' . $this->name,
            ],
            'en' => [
                'body' => "new contact message from $this->name"
            ]
        ];
    }
}
