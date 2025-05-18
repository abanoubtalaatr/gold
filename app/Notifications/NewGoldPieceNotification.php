<?php

namespace App\Notifications;

use App\Models\GoldPiece;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewGoldPieceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $goldPiece;

    public function __construct(GoldPiece $goldPiece)
    {
        $this->goldPiece = $goldPiece;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'gold_piece_id' => $this->goldPiece->id,
            'type' => $this->goldPiece->type,
            'name' => $this->goldPiece->name,
            'price' => $this->goldPiece->type === 'for_rent' 
                ? $this->goldPiece->rental_price_per_day 
                : $this->goldPiece->sale_price,
            'ar' => [
                'body' => 'قطعة ذهب جديدة متاحة: ' . $this->goldPiece->name,
            ],
            'en' => [
                'body' => "New gold piece available: {$this->goldPiece->name}"
            ]
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'gold_piece_id' => $this->goldPiece->id,
            'type' => $this->goldPiece->type,
            'name' => $this->goldPiece->name,
            'price' => $this->goldPiece->type === 'for_rent' 
                ? $this->goldPiece->rental_price_per_day 
                : $this->goldPiece->sale_price,
            'message' => "New gold piece available: {$this->goldPiece->name}"
        ]);
    }
} 