<?php

namespace App\Http\Controllers\Api\V1\GoldPiece;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\GoldPiece;
use App\Models\OrderRental;
use App\Http\Resources\Api\GoldPieceResource;
use App\Http\Resources\Api\OrderSaleResource;
use App\Models\OrderSale;

class ConfirmSoldToVendorController extends Controller
{
    use ApiResponseTrait;

    public function index(OrderSale $order)
    {
        $order->update(['status' => OrderRental::STATUS_SOLD]);

        return $this->successResponse(OrderSaleResource::make($order), __("mobile.piece_sold_to_vendor_successfully"));
    }
}
