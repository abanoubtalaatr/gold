<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Models\CanceledOrder;
use App\Models\SystemSetting;
use App\Events\OrderRentalEvent;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\VendorNotificationService;
use App\Http\Resources\Api\OrderSaleResource;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\OrderRentalResource;
use App\Http\Requests\Api\V1\Order\UpdateOrderSaleRequest;
use App\Http\Requests\Api\V1\Order\UpdateOrderRentalRequest;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $vendorNotificationService;

    public function __construct(VendorNotificationService $vendorNotificationService)
    {
        $this->vendorNotificationService = $vendorNotificationService;
    }

    public function index(Request $request)
    {
        $query = OrderRental::where('user_id', Auth::id())
            ->where('type', OrderRental::LEASE_TYPE)
            ->with('goldPiece');

        // Apply status filter if provided
        if ($request->status) {
            $query->where('status', $request->status);
        }
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
        $goldPiece = GoldPiece::with('user')->findOrFail($request->gold_piece_id);

        $existingOrderRental = OrderRental::where('gold_piece_id', $goldPiece->id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            })
            ->exists();

        if ($existingOrderRental) {
            return $this->errorResponse(__("mobile.gold_piece_already_rented"), [], 422);
        }

        // Get user's address to find appropriate branches
        $user = User::with('addresses')->find(Auth::id());
        if (!$user) {
            return $this->errorResponse(__('mobile.User not found'), [], 404);
        }

        $address = $user->addresses()
            ->where('is_default', true)
            ->orWhere(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orderBy('created_at', 'asc');
            })
            ->first();

        if (!$address) {
            return $this->errorResponse(
                __('mobile.User must have at least one address to create a rental order'),
                [],
                422
            );
        }

        $orderRental = OrderRental::where('gold_piece_id', $goldPiece->id)->where('status', OrderRental::STATUS_VENDOR_ALREADY_TAKE_THE_PIECE)->first();

        $availableBranch = $orderRental->branch;

        if (!$availableBranch) {
            return $this->errorResponse(__("mobile.no_available_branch"), [], 422);
        }

        DB::beginTransaction();

        // Create the order rental
        $orderRental = OrderRental::create([
            'user_id' => Auth::id(),
            'gold_piece_id' => $goldPiece->id,
            'start_date' => $request->start_date,
            'branch_id' => $availableBranch->id,
            'end_date' => $request->end_date,
            'total_price' => $request->total_price,
            'type' => OrderRental::LEASE_TYPE,
            'status' => OrderRental::STATUS_PENDING_APPROVAL
        ]);

        DB::commit();

        // Send vendor notifications for the created order
        try {
            // Use VendorNotificationService for vendor notifications with real-time features
            $this->vendorNotificationService->notifyVendorOfNewOrder($orderRental, 'lease');
        } catch (\Exception $e) {
        }

        // Load the goldPiece relationship before returning the resource
        $orderRental->load('goldPiece');
        return $this->successResponse(new OrderRentalResource($orderRental), __("mobile.order_created_success"), 200);
    }

    public function updateRental(UpdateOrderRentalRequest $request, OrderRental $order)
    {
        $goldPiece = GoldPiece::with('user')->findOrFail($order->gold_piece_id);
        // should ensure the order is approved
        // if ($order->status !== OrderRental::STATUS_APPROVED) {
        //     return $this->errorResponse(__("mobile.order_not_approved"), [], 422);
        // }

        if ($order->type === OrderRental::LEASE_TYPE && !$request->filled('start_date') && !$request->filled('end_date')) {
            return $this->errorResponse(__("mobile.start_and_end_date_required"), [], 422);
        }

        if ($order->type === OrderRental::LEASE_TYPE) {
            $order->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_price' => $request->total_price,
            ]);
        } else {
            $order->update([
                'total_price' => $request->total_price,
            ]);
        }

        if ($request->hasFile('images')) {
            // Clear existing images before adding new ones
            $goldPiece->clearMediaCollection('images');

            foreach ($request->file('images') as $image) {
                try {
                    $goldPiece->addMedia($image)
                        ->toMediaCollection('images', 'public');
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $goldPiece->update([
            'name' => $request->name,
            'weight' => $request->weight,
            'carat' => $request->carat,
            'description' => $request->description,
            'rental_price_per_day' => $request->rental_price_per_day,
            'is_including_lobes' => $request->is_including_lobes,
        ]);

        // Return the updated order with type information for frontend validation
        $order->load('goldPiece');
        return $this->successResponse(
            new OrderRentalResource($order),
            __("mobile.order_updated_success"),
            200
        );

        // Load the goldPiece relationship before returning the resource
        $orderRental->load('goldPiece');
        return $this->successResponse(new OrderRentalResource($orderRental), __("mobile.order_created_success"), 200);
    }

    public function updateSale(UpdateOrderSaleRequest $request, OrderSale $order)
    {
        $goldPiece = GoldPiece::with('user')->findOrFail($order->gold_piece_id);
        // should ensure the order is approved
        // if ($order->status !== OrderRental::STATUS_APPROVED) {
        //     return $this->errorResponse(__("mobile.order_not_approved"), [], 422);
        // }

        $order->update([
            'total_price' => $request->total_price,
        ]);


        if ($request->hasFile('images')) {
            // Clear existing images before adding new ones
            $goldPiece->clearMediaCollection('images');

            foreach ($request->file('images') as $image) {
                try {
                    $goldPiece->addMedia($image)
                        ->toMediaCollection('images', 'public');
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $goldPiece->update([
            'name' => $request->name,
            'weight' => $request->weight,
            'carat' => $request->carat,
            'description' => $request->description,
            'rental_price_per_day' => $request->rental_price_per_day,
            'is_including_lobes' => $request->is_including_lobes,
        ]);

        // Return the updated order with type information for frontend validation
        $order->load('goldPiece');
        return $this->successResponse(
            new OrderSaleResource($order),
            __("mobile.order_updated_success"),
            200
        );

        // Load the goldPiece relationship before returning the resource
        $orderRental->load('goldPiece');
        return $this->successResponse(new OrderSaleResource($orderRental), __("mobile.order_created_success"), 200);
    }
    public function toggleSuspendRental(OrderRental $order)
    {
        $order->update(['is_suspended' => !$order->is_suspended]);
        return $this->successResponse($order, __("mobile.order_updated_success"));
    }

    public function toggleSuspendSale(OrderSale $order)
    {
        $order->update(['is_suspended' => !$order->is_suspended]);
        return $this->successResponse($order, __("mobile.order_updated_success"));
    }

    public function show(OrderRental $order)
    {
        return $this->successResponse(new OrderRentalResource($order), __("mobile.order_fetched_success"), 200);
    }

    public function deleteRental(OrderRental $order)
    {
        $order->delete();
        return $this->successResponse(null, __("mobile.order_deleted_success"));
    }

    public function deleteSale(OrderSale $order)
    {
        $order->delete();
        return $this->successResponse(null, __("mobile.order_deleted_success"));
    }

    public function cancel(OrderSale $order)
    {
        $canceledOrder = CanceledOrder::where('user_id', Auth::id())->first();
       
        if (!$canceledOrder) {
            $canceledOrder = CanceledOrder::create([
                'user_id' => Auth::id(),
                'count' => 1
            ]);
        }else{
            $canceledOrder->increment('count');
        }
        $maxCanceledOrders = SystemSetting::first()->max_canceled_orders;

   
        if($canceledOrder->count >= $maxCanceledOrders){
          Auth::logout();
          return $this->errorResponse(__("mobile.account_suspended_because_you_exceeded_the_maximum_number_of_canceled_orders"), [], 422);
        }
    
        return $this->successResponse(null, __("mobile.order_canceled_success"));
    }
}
