<?php

namespace App\Http\Resources\Api;

use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GoldPieceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $rentals = $this->orderRentals ? $this->orderRentals() : null;
        $rental = $rentals ? $rentals->first() : null;
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
            'description' => $this->description,
            'qr_code' => $this->qr_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
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
            'branch' => $this->branchDetails() ? new BranchResource($this->branchDetails()) : null,
            'remaining_days' => $remainingDays,
            'total_days' => $totalDays,
            'city' => $this->branchDetails() ? SimpleCityResource::make($this->branchDetails()->city) : null,
            'days_left_to_return' => number_format(now()->diffInDays($rental?->end_date, false), 0),
            'contacts' => ContactResource::collection($rental?->contacts ?? collect()),
            'order_status' => $rental?->status,
            'start_date' => $rental?->start_date ?? Carbon::today(),
            'end_date' => $rental?->end_date ?? Carbon::today()->addDays(3),
            'price_delay' => 200,
            'is_suspended' => $this->is_suspended,
        ];
    }

    public function branchDetails()
    {
        $rentalBranch = $this->orderRentals ? $this->orderRentals()->first()?->branch : null;
        $saleBranch = $this->orderSales ? $this->orderSales()->first()?->branch : null;
        return $rentalBranch ?? $saleBranch;
    }
}