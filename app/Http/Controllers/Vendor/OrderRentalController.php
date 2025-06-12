<?php

namespace App\Http\Controllers\Vendor;

use Inertia\Inertia;
use App\Models\Branch;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RentalWorkflowService;
use App\Events\OrderRentalStatusChangeEvent;
use App\Notifications\Client\GoldPieceAcceptedNotification;
use App\Notifications\Client\GoldPieceRejectedNotification;

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
        // Notify gold piece owner
        $goldPieceUser = $order->goldPiece?->user;

        if ($goldPieceUser) {
            $goldPieceUser->notify(new GoldPieceAcceptedNotification($order, Auth::user()->name));
        }

        $order->update(['status' => OrderRental::STATUS_APPROVED]);

        event(new OrderRentalStatusChangeEvent($order));

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $orderId)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        $order->update(['status' => 'rented']);

        // Notify gold piece owner
        $goldPieceUser = $order->goldPiece?->user;
        event(new OrderRentalStatusChangeEvent($order));
        if ($goldPieceUser) {
            $goldPieceUser->notify(
                new GoldPieceRejectedNotification($order, Auth::user()->name)
            );
        }

        return back();
    }

    public function updateStatus(Request $request, $orderId)
    {
        $order = OrderRental::with(['user', 'branch', 'goldPiece'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        // Validate the incoming status
        $request->validate(['status' => 'required|in:pending_approval,approved,piece_sent,rented,available,sold,rejected',]);

        $newStatus = $request->status;

        // Use workflow service for proper status management and notifications
        $this->rentalWorkflowService->updateStatus($order, $newStatus, Auth::user());


        return back()->with('success', __('Order status updated successfully'));
    }

    protected function authorizeVendor($order)
    {
        $vendorBranches = Branch::where('vendor_id', Auth::id())->pluck('id');
        if (!$vendorBranches->contains($order->branch_id) && $order->status !== OrderRental::STATUS_PENDING_APPROVAL) {
            abort(403, 'Unauthorized action.');
        }
    }
}
