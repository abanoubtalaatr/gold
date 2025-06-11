<?php

namespace App\Http\Resources\Api;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\BranchResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GoldPieceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $rental = $this->orderRentals ? $this->orderRentals()->first() : null;
        $sale = $this->orderSales ? $this->orderSales()->first() : null;
        $order = $rental ?? $sale; // Use rental or sale, whichever exists
        $remainingDays = 0;
        $totalDays = 0;

        if ($order && $order->end_date) {
            $diff = (new DateTime())->diff(new DateTime($order->end_date));
            $remainingDays = $diff->invert ? 0 : $diff->days;
        }

        if ($order && $order->start_date && $order->end_date) {
            $totalDays = (new DateTime($order->start_date))->diff(new DateTime($order->end_date))->days;
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
            'qr_code_media' => $this->getMedia('qr_codes')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'name' => $media->name,
                ];
            })->first(),
            'rating' => [
                'average' => $this->average_rating,
                'count' => $this->rating_count,
            ],
            'is_favorited' => $request->user() ? $this->favoritedBy()->where('user_id', $request->user()->id)->exists() : false,
            'branch' => $this->branchDetails() ? new BranchResource($this->branchDetails()) : null,
            'remaining_days' => $remainingDays,
            'total_days' => $totalDays,
            'city' => $this->branchDetails() ? SimpleCityResource::make($this->branchDetails()->city) : null,
            'days_left_to_return' => number_format(now()->diffInDays($order?->end_date, false), 0),
            'contacts' => ContactResource::collection($order?->contacts ?? collect()),
            'order_status' => $order?->status,
            'start_date' => $order?->start_date ?? Carbon::today(),
            'end_date' => $order?->end_date ?? Carbon::today()->addDays(3),
            'price_delay' => 200,
            'is_suspended' => $order?->is_suspended,
            'order_id'=>  $order?->id,
            'qr' => $this->qr_code,

            'invoice_url' => $order?->id ? route('api.v1.invoice.show', $order->id) : null,
        ];
    }

    public function branchDetails()
    {
        $rentalBranch = $this->orderRentals ? $this->orderRentals()->first()?->branch : null;
        $saleBranch = $this->orderSales ? $this->orderSales()->first()?->branch : null;
        return $rentalBranch ?? $saleBranch;
    }
}