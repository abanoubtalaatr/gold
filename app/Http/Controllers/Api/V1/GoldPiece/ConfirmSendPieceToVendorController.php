<?php

namespace App\Http\Controllers\Api\V1\GoldPiece;

use App\Models\GoldPiece;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GoldPieceResource;
use App\Http\Resources\Api\OrderRentalResource;

class ConfirmSendPieceToVendorController extends Controller
{
    use ApiResponseTrait;
    
    public function index(OrderRental $order)
    {
        $order->update(['status' => OrderRental::STATUS_PIECE_SENT]);

        return $this->successResponse(OrderRentalResource::make($order), __("mobile.piece_sent_to_vendor_successfully"));
    }
}
