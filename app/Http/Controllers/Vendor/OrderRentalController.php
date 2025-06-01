<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Notifications\Client\GoldPieceAcceptedNotification;
use App\Notifications\Client\GoldPieceRejectedNotification;
use App\Services\RentalWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderRentalController extends Controller
{
    protected $rentalWorkflowService;

    public function __construct(RentalWorkflowService $rentalWorkflowService)
    {
        $this->rentalWorkflowService = $rentalWorkflowService;
    }

    public function index(Request $request)
    {
        $vendorId = Auth::id();
        $branchIds = Branch::where('vendor_id', $vendorId)->pluck('id');
        $branches = Branch::where('vendor_id', $vendorId)->select('id', 'name')->get();

        $filters = $request->only(['search', 'branch_id', 'status']);

        $rentalOrdersQuery = OrderRental::query()->where('type', OrderRental::RENT_TYPE)
            ->whereIn('branch_id', $branchIds)
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('goldPiece', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($filters['branch_id'] ?? null, function ($query, $branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                // Handle all possible status values from the frontend
                $query->where('status', $status);
            })
            ->with(['user', 'goldPiece', 'goldPiece.user', 'branch'])
            ->orderBy('created_at', 'desc');

        $rentalOrders = $rentalOrdersQuery->paginate(10)->appends($filters);

        // $rentalOrders->getCollection()->transform(function ($order) {
        //     if ($order->goldPiece) {
        //         $order->goldPiece->qr_code = base64_encode(
        //             QrCode::format('png')->size(100)->generate(
        //                 route('vendor.gold-pieces.show', $order->goldPiece->id)
        //             )
        //         );
        //     }
        //     return $order;
        // });

        return Inertia::render('Vendor/Orders/RentalIndex', [
            'rentalOrders' => $rentalOrders,
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function accept(Request $request, $orderId)
    {

        $order = OrderRental::with(['goldPiece', 'goldPiece.user'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $order->update([
            'branch_id' => $request->branch_id,
            'status' => OrderRental::STATUS_APPROVED,
        ]);

        // Notify gold piece owner
        $goldPieceUser = $order->goldPiece?->user;
        if ($goldPieceUser) {
            $goldPieceUser->notify(
                new GoldPieceAcceptedNotification($order, auth()->user()->name)
            );
        }
        Log::info('Order accepted', ['order_id' => $order->id, 'vendor_id' => Auth::id()]);

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $orderId)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        $order->update(['status' => 'rejected']);

        // Notify gold piece owner
        $goldPieceUser = $order->goldPiece?->user;
        if ($goldPieceUser) {
            $goldPieceUser->notify(
                new GoldPieceRejectedNotification($order, auth()->user()->name)
            );
        }
        Log::info('Order rejected', ['order_id' => $order->id, 'vendor_id' => Auth::id()]);

        return back()->with('success', __('Order rejected successfully'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        try {
            $order = OrderRental::with(['user', 'branch', 'goldPiece'])->findOrFail($orderId);
            $this->authorizeVendor($order);
            
            // Validate the incoming status
            $request->validate([
                'status' => 'required|in:pending_approval,approved,piece_sent,rented,available,sold,rejected',
            ]);

            $newStatus = $request->status;
            $oldStatus = $order->status;
            
            // Use workflow service for proper status management and notifications
            $this->rentalWorkflowService->updateStatus($order, $newStatus, Auth::user());

            Log::info('Order status updated via workflow service', [
                'order_id' => $order->id, 
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'vendor_id' => Auth::id()
            ]);

            return back()->with('success', __('Order status updated successfully'));
            
        } catch (\Exception $e) {
            Log::error('Failed to update order status', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('Failed to update order status. Please try again.'));
        }
    }

    protected function authorizeVendor($order)
    {
        $vendorBranches = Branch::where('vendor_id', Auth::id())->pluck('id');
        if (!$vendorBranches->contains($order->branch_id) && $order->status !== OrderRental::STATUS_PENDING_APPROVAL) {
            abort(403, 'Unauthorized action.');
        }
    }





}
