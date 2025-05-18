<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderRentalResource;
use App\Http\Requests\Api\V1\StoreOrderRequest;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $goldPiece = GoldPiece::findOrFail($request->gold_piece_id);

        if ($goldPiece->status !== 'available') {
            return response()->json(['message' => 'Gold piece is not available'], 422);
        }

        if ($goldPiece->type === 'for_rent') {
            $existingOrderRental = OrderRental::where('gold_piece_id', $goldPiece->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                })
                ->exists();

            if ($existingOrderRental) {
                return response()->json(['message' => 'Gold piece is already rented for the selected dates'], 422);
            }

            $days = (new \DateTime($request->end_date))->diff(new \DateTime($request->start_date))->days;
            $totalPrice = $days * $goldPiece->rental_price_per_day;

            $orderRental = OrderRental::create([
                'user_id' => auth()->id(),
                'gold_piece_id' => $goldPiece->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            return new OrderRentalResource($orderRental->load('goldPiece'));
        }

        if ($goldPiece->type === 'for_sale') {
            $orderSale = OrderSale::create([
                'user_id' => auth()->id(),
                'gold_piece_id' => $goldPiece->id,
                'total_price' => $goldPiece->sale_price,
                'status' => 'pending',
            ]);

            return new OrderSaleResource($orderSale->load('goldPiece'));
        }

        return response()->json(['message' => 'Invalid gold piece type'], 422);
    }

    public function index(Request $request)
    {
        $orderRentals = OrderRental::where('user_id', auth()->id())->with('goldPiece')->get();
        $orderSales = OrderSale::where('user_id', auth()->id())->with('goldPiece')->get();

        return [
            'rentals' => OrderRentalResource::collection($orderRentals),
            'sales' => OrderSaleResource::collection($orderSales),
        ];
    }

    public function myRequests()
    {
        $orderRentals = OrderRental::where('user_id', auth()->id())
            ->whereHas('goldPiece', fn($q) => $q->whereNotNull('branch_id'))
            ->with('goldPiece')
            ->get();

        $orderSales = OrderSale::where('user_id', auth()->id())
            ->whereHas('goldPiece', fn($q) => $q->whereNotNull('branch_id'))
            ->with('goldPiece')
            ->get();

        return [
            'rentals' => OrderRentalResource::collection($orderRentals),
            'sales' => OrderSaleResource::collection($orderSales),
        ];
    }
}