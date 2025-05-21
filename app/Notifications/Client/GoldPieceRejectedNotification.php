<?php

namespace App\Notifications\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoldPieceRejectedNotification extends Notification
{
    use Queueable;
    protected $order;
    protected $vendorName;

    public function __construct($order, $vendorName)
    {
        $this->order = $order;
        $this->vendorName = $vendorName;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $goldPiece = $this->order->goldPiece;

        return [
            'title' => [
                'ar' => 'تم رفض طلب القطعة الذهبية',
                'en' => 'Gold Piece Request Rejected'
            ],
            'message' => [
                'ar' => "تم رفض طلب قطعتك الذهبية {$goldPiece->name} من قبل البائع {$this->vendorName}",
                'en' => "Your gold piece {$goldPiece->name} has been rejected by vendor {$this->vendorName}"
            ],
            'type' => 'gold_piece_rejected',
            'data' => [
                'order_id' => $this->order->id,
                'gold_piece_id' => $goldPiece->id,
                // 'action_url' => route('user.orders.show', $this->order->id)
            ]
        ];
    }
}