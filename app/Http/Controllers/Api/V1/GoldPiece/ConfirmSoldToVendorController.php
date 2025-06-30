<?php

namespace App\Http\Controllers\Api\V1\GoldPiece;

use App\Models\OrderSale;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Services\TransactionsService;
use App\Http\Resources\Api\OrderSaleResource;

class ConfirmSoldToVendorController extends Controller
{
    use ApiResponseTrait;

    public function update(Request $request, $order)
    {
        $order = OrderSale::find($order);

        if (!$order) {
            return $this->errorResponse(__("mobile.order_not_found"), 404);
        }
        if ($order->status == OrderSale::STATUS_CONFIRM_SOLD_FROM_VENDOR) {
            (new TransactionsService())->addTransactionForVendorAndPlatform($order, 'sale', $order->total_price, $order->branch->vendor_id, 'credit');
        }

        $order->update(['status' => OrderSale::STATUS_SOLD]);

        return $this->successResponse(OrderSaleResource::make($order), __("mobile.piece_sold_to_vendor_successfully"));
    }
}
