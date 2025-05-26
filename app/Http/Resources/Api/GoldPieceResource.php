<?php

namespace App\Http\Resources\Api;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\V1\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GoldPieceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
            'description' => $this->description,
            'qr_code' => $this->qr_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'images' => $this->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumbnail' => $media->getUrl('thumb'),
                    'medium' => $media->getUrl('medium'),
                ];
            }),
            'rating' => [
                'average' => $this->average_rating,
                'count' => $this->rating_count,
            ],
            'is_favorited' => $request->user() ? $this->favoritedBy()->where('user_id', $request->user()->id)->exists() : false,
          
            //return the branch that accept the gold piece
            'branch' => new BranchResource($this->branch),
        ];
    }
}