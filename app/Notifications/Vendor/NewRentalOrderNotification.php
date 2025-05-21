<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewRentalOrderNotification extends Notification
{
    use Queueable;

  
    protected $orderRental;

    public function __construct($orderRental)
    {
        $this->orderRental = $orderRental;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $goldPiece = $this->orderRental->goldPiece;
        $user = $this->orderRental->user;
        
        return [
            'title' => [
                'ar' => 'حجز جديد',
                'en' => 'New Rental Order'
            ],
            'description' => [
                'ar' => "تم استلام حجز جديد للقطعة: {$goldPiece->name} من العميل: {$user->name}",
                'en' => "New rental order received for piece: {$goldPiece->name} from customer: {$user->name}"
            ],
            'type' => 'new_rental_order',
            'data' => [
                'order_id' => $this->orderRental->id,
                'gold_piece_id' => $goldPiece->id,
                'action_url' => route('vendor.orders.rental.index', $this->orderRental->id)
            ]
        ];
    }
}