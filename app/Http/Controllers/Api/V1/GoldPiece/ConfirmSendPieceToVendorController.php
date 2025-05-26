<?php

namespace App\Http\Controllers\Api\v1\GoldPiece;

use App\Models\GoldPiece;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GoldPieceResource;

class ConfirmSendPieceToVendorController extends Controller
{
    use ApiResponseTrait;
    
    public function __invoke(GoldPiece $goldPiece)
    {
        $goldPiece->update(['status' => OrderRental::STATUS_PIECE_SENT]);

        return $this->successResponse(GoldPieceResource::make($goldPiece), __("mobile.piece_sent_to_vendor_successfully"));
    }
}
