<?php

namespace App\Http\Controllers\Api\V1\GoldPiece;

use App\Models\User;
use App\Models\Wallet;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\TransactionsService;
use App\Http\Resources\Api\GoldPieceResource;
use App\Http\Resources\Api\OrderSaleResource;

class ConfirmSoldToVendorController extends Controller
{
    use ApiResponseTrait;

    public function index(OrderSale $order)
    {
        if ($order->status == OrderSale::STATUS_CONFIRM_SOLD_FROM_VENDOR) {
            (new TransactionsService())->addTransactionForVendorAndPlatform($order, 'sale', $order->total_price, $order->branch->vendor_id, 'credit');
        }

        $order->update(['status' => OrderSale::STATUS_SOLD]);

        return $this->successResponse(OrderSaleResource::make($order), __("mobile.piece_sold_to_vendor_successfully"));
    }
}
