<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\OrderRentalEvent;
use App\Notifications\OrderRentalNotification;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\OrderRentalResource;

class OrderController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request)
    {
        $query = OrderRental::where('user_id', Auth::id())->where('type', OrderRental::LEASE_TYPE)
            ->with('goldPiece');

        // Get sort parameters from request with defaults
        $orderColumn = $request->input('order_column', 'status');
        $orderType = $request->input('order_type', 'asc');

        // Validate order type
        $orderType = strtolower($orderType) === 'desc' ? 'desc' : 'asc';

        // Apply sorting
        $orderRentals = $query->orderBy($orderColumn, $orderType)->get();

        return $this->successResponse(OrderRentalResource::collection($orderRentals), __("mobile.fetch_orders_success"));
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $goldPiece = GoldPiece::findOrFail($request->gold_piece_id);

            $existingOrderRental = OrderRental::where('gold_piece_id', $goldPiece->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
                })
                ->exists();

            if ($existingOrderRental) {
                return $this->errorResponse(__("mobile.gold_piece_already_rented"), [], 422);
            }

            // Get the rental order that has this gold piece
            $rentalOrder = OrderRental::where('gold_piece_id', $goldPiece->id)
                ->where('type', OrderRental::RENT_TYPE)
                ->first();

            if (!$rentalOrder) {
                return $this->errorResponse(__("mobile.no_available_branch"), [], 422);
            }

            // Get the actual Branch model
            $branch = Branch::findOrFail($rentalOrder->branch_id);

            Log::info('Found branch for notification:', [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name
            ]);

            // Create the order rental
            $orderRental = OrderRental::create([
                'user_id' => Auth::id(),
                'gold_piece_id' => $goldPiece->id,
                'start_date' => $request->start_date,
                'branch_id' => $branch->id,
                'end_date' => $request->end_date,
                'total_price' => $request->total_price,
                'type'=> OrderRental::LEASE_TYPE,
                'status' => 'pending',
            ]);

            // Send notification and broadcast event
            try {
                $branch->notify(new OrderRentalNotification($orderRental));
                Log::info('Notification sent successfully', [
                    'branch_id' => $branch->id,
                    'order_id' => $orderRental->id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send notification:', [
                    'error' => $e->getMessage(),
                    'branch_id' => $branch->id,
                    'order_id' => $orderRental->id
                ]);
            }

            broadcast(new OrderRentalEvent($orderRental, $branch->id))->toOthers();

            return new OrderRentalResource($orderRental->load('goldPiece'));
        } catch (\Exception $e) {
            Log::error('Error in store method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(__("mobile.order_creation_failed"), [], 500);
        }
    }
}