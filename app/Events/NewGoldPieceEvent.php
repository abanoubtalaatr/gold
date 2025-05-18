<?php

namespace App\Events;

use App\Models\GoldPiece;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewGoldPieceEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $goldPiece;
    public $branchId;

    public function __construct(GoldPiece $goldPiece, int $branchId)
    {
        $this->goldPiece = $goldPiece;
        $this->branchId = $branchId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('branch.' . $this->branchId);
    }

    public function broadcastAs()
    {
        return 'new.gold.piece';
    }

    public function broadcastWith()
    {
        return [
            'gold_piece_id' => $this->goldPiece->id,
            'type' => $this->goldPiece->type,
            'name' => $this->goldPiece->name,
            'price' => $this->goldPiece->type === 'for_rent' 
                ? $this->goldPiece->rental_price_per_day 
                : $this->goldPiece->sale_price,
            'message' => "New gold piece available: {$this->goldPiece->name}"
        ];
    }
} 