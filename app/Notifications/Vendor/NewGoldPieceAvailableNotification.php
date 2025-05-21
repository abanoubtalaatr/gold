<?php

namespace App\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewGoldPieceAvailableNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $goldPiece;

    public function __construct($goldPiece)
    {
        $this->goldPiece = $goldPiece;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $type = $this->goldPiece->type == 'for_rent' ? 'للإيجار' : 'للبيع';
        $enType = $this->goldPiece->type == 'for_rent' ? 'for rent' : 'for sale';

        return [
            'title' => [
                'ar' => 'قطعة ذهب جديدة متاحة',
                'en' => 'New Gold Piece Available'
            ],
            'description' => [
                'ar' => "قطعة ذهب جديدة $type: {$this->goldPiece->name} - الوزن: {$this->goldPiece->weight} جرام",
                'en' => "New gold piece $enType: {$this->goldPiece->name} - Weight: {$this->goldPiece->weight} grams"
            ],
            'type' => 'new_gold_piece_available',
            'data' => [
                'gold_piece_id' => $this->goldPiece->id,
                'action_url' => route('vendor.gold-pieces.show', $this->goldPiece->id)
            ]
        ];
    }
}