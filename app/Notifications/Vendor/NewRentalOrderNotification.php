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
    protected $branch;

    public function __construct($orderRental, $branch)
    {
        $this->orderRental = $orderRental;
        $this->branch = $branch;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $goldPiece = $this->orderRental->goldPiece;
        $customer = $this->orderRental->user;

        return [
            'title' => [
                'ar' => 'حجز جديد',
                'en' => 'New Rental Order'
            ],
            'message' => [
                'ar' => "حجز جديد للقطعة {$goldPiece->name} من العميل {$customer->name} في فرع {$this->branch->name}",
                'en' => "New rental for {$goldPiece->name} from customer {$customer->name} at {$this->branch->name} branch"
            ],
            'type' => 'new_rental_order',
            'data' => [
                'order_id' => $this->orderRental->id,
                'branch_id' => $this->branch->id,
                'gold_piece_id' => $goldPiece->id,
                'action_url' => route('vendor.orders.rental.index', $this->orderRental->id)
            ]
        ];
    }
}