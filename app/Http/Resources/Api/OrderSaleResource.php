<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderSaleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'gold_piece' => new GoldPieceResource($this->whenLoaded('goldPiece')),
            'total_price' => $this->total_price,
            'status' => $this->status,
        ];
    }
}