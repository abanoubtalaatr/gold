<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordOTP extends Notification implements ShouldQueue
{
    use Queueable;

    protected $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Password Verification Code')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Your verification code is: ' . $this->code)
            ->line('This code will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
} 