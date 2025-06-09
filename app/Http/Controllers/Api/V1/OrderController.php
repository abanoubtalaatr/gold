<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\OrderRentalEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\OrderRentalResource;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Models\User;
use App\Services\VendorNotificationService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Find active branches in the same city that could handle this gold piece

        $availableBranches = null;

        $orderRental = OrderRental::where('gold_piece_id', $goldPiece->id)->where('status', OrderRental::STATUS_APPROVED)->first();
        
        $availableBranch = $orderRental->branch;
        
        if (!$availableBranch) {
            return $this->errorResponse(__("mobile.no_available_branch"), [], 422);
        }

        // For now, we'll use the first available branch
        // In a more complex system, you might want to implement branch selection logic

        

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

            Log::info('Vendor notification sent successfully for new rental order', [
                'order_id' => $orderRental->id,
                'vendor_id' => $availableBranch->vendor_id,
                'branch_id' => $availableBranch->id,
                'order_type' => 'lease',
            ]);
        } catch (\Exception $e) {
            // Log notification failure but don't fail the entire request
            Log::error('Failed to send vendor notification for new rental order', [
                'order_id' => $orderRental->id,
                'vendor_id' => $availableBranch->vendor_id ?? null,
                'branch_id' => $availableBranch->id,
                'order_type' => 'lease',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Load the goldPiece relationship before returning the resource
        $orderRental->load('goldPiece');
        return $this->successResponse(new OrderRentalResource($orderRental), __("mobile.order_created_success"), 200);
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
}
