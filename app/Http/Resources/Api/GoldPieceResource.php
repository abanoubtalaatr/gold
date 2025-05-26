<?php

namespace App\Http\Resources\Api;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\V1\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GoldPieceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $rental = $this->orderRentals()->first();
        $remainingDays = 0;
        $totalDays = 0;

        if ($rental && $rental->end_date) {
            $diff = (new DateTime())->diff(new DateTime($rental->end_date));
            $remainingDays = $diff->invert ? 0 : $diff->days;
        }

        if ($rental && $rental->start_date && $rental->end_date) {
            $totalDays = (new DateTime($rental->start_date))->diff(new DateTime($rental->end_date))->days;
        }

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
            'branch' => new BranchResource($this->branchDetails()),
            'start_date' => $rental?->start_date,
            'end_date' => $rental?->end_date,
            'remaining_days' => $remainingDays,
            'total_days' => $totalDays,
            'city' => SimpleCityResource::make($this->branchDetails()->city),
        ];
    }

    public function branchDetails()
    {
        return $this->orderRentals()->first()?->branch ?? $this->orderSales()->first()?->branch;
    }
}