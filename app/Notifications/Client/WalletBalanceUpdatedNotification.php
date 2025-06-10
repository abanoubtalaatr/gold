<?php

namespace App\Notifications\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletBalanceUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transactionType;
    protected $amount;
    protected $description;
    protected $newBalance;

    /**
     * Create a new notification instance.
     */
    public function __construct($transactionType, $amount, $description, $newBalance)
    {
        $this->transactionType = $transactionType;
        $this->amount = $amount;
        $this->description = $description;
        $this->newBalance = $newBalance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $operationType = $this->transactionType === 'credit' ? 'إضافة' : 'خصم';
        $operationText = $this->transactionType === 'credit' ? 'تم إضافة' : 'تم خصم';
        
        return (new MailMessage)
            ->subject('تحديث رصيد المحفظة - ' . $operationType)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line($operationText . ' مبلغ ' . number_format($this->amount, 2) . ' ريال ' . ($this->transactionType === 'credit' ? 'إلى' : 'من') . ' محفظتك.')
            ->line('السبب: ' . $this->description)
            ->line('رصيدك الحالي: ' . number_format($this->newBalance, 2) . ' ريال')
            ->line('شكراً لاستخدامك خدماتنا!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $operationType = $this->transactionType === 'credit' ? 'إضافة' : 'خصم';
        $operationText = $this->transactionType === 'credit' ? 'تم إضافة' : 'تم خصم';
        
        return [
            'type' => 'wallet_balance_updated',
            'title' => 'تحديث رصيد المحفظة',
            'message' => $operationText . ' مبلغ ' . number_format($this->amount, 2) . ' ريال ' . ($this->transactionType === 'credit' ? 'إلى' : 'من') . ' محفظتك',
            'description' => $this->description,
            'amount' => $this->amount,
            'transaction_type' => $this->transactionType,
            'new_balance' => $this->newBalance,
            'operation_type' => $operationType,
        ];
    }
} 