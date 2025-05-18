<?php

namespace App\Http\Resources\Api;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class GoldPieceResource extends JsonResource
{
    public function toArray($request)
    {
        $user = Auth::user();
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'weight' => $this->weight,
            'carat' => $this->carat,
            'rental_price_per_day' => $this->rental_price_per_day,
            'sale_price' => $this->sale_price,
            'deposit_amount' => $this->deposit_amount,
            'type' => $this->type,
            'status' => $this->status,
            'qr_code' => $this->qr_code,
            'description' => $this->description,
            'images' => $this->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'original' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'medium' => $media->getUrl('medium'),
                ];
            }),
            'user' => new UserResource($this->whenLoaded('user')),
            'is_favorited' => $user ? $user->favorites()->where('gold_piece_id', $this->id)->exists() : false,
            // 'branch' => new BranchResource($this->whenLoaded('branch')),
        ];
    }
}