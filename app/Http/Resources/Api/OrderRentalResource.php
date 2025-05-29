<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRentalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'gold_piece' => new GoldPieceResource($this->goldPiece),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_price' => $this->total_price,
            'days_left_to_return' => number_format(now()->diffInDays($this->end_date, false), 0),
            'status' => $this->status,
            'contacts' => ContactResource::collection($this->contacts),
            'is_suspended'=> $this->is_suspended,
        ];
    }
}