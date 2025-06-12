<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Branch;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\RentalWorkflowService;
use App\Events\OrderRentalStatusChangeEvent;
use App\Notifications\Client\GoldPieceAcceptedNotification;
use App\Notifications\Client\GoldPieceRejectedNotification;

class AdminOrderRentalController extends Controller
{
    protected $rentalWorkflowService;

    public function __construct(RentalWorkflowService $rentalWorkflowService)
    {
        $this->rentalWorkflowService = $rentalWorkflowService;
    }

    public function index(Request $request)
    {
        $branches = Branch::select('id', 'name')->get();

        $filters = $request->only(['search', 'branch_id', 'status']);

        $rentalOrdersQuery = OrderRental::query()->where('type', OrderRental::RENT_TYPE)
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
                $query->where('status', $status);
            })
            ->with(['user', 'goldPiece', 'goldPiece.user', 'branch'])
            ->orderBy('created_at', 'desc');

        $rentalOrders = $rentalOrdersQuery->paginate(10)->appends($filters);
        return Inertia::render('Admin/Orders/RentalIndex', [
            'orders' => $rentalOrders,
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function accept(Request $request, $orderId)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user'])->findOrFail($orderId);

        // Notify gold piece owner
        $goldPieceUser = $order->goldPiece?->user;

        if ($goldPieceUser) {
            $goldPieceUser->notify(new GoldPieceAcceptedNotification($order, 'Admin'));
        }

        $order->update(['status' => OrderRental::STATUS_APPROVED]);

        event(new OrderRentalStatusChangeEvent($order));

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $orderId)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user'])->findOrFail($orderId);

        $order->update(['status' => 'rented']);

        // Notify gold piece owner
        $goldPieceUser = $order->goldPiece?->user;
        event(new OrderRentalStatusChangeEvent($order));
        if ($goldPieceUser) {
            $goldPieceUser->notify(
                new GoldPieceRejectedNotification($order, 'Admin')
            );
        }

        return back();
    }

    public function updateStatus(Request $request, $orderId)
    {
        $order = OrderRental::with(['user', 'branch', 'goldPiece'])->findOrFail($orderId);

        $request->validate(['status' => 'required|in:pending_approval,approved,piece_sent,rented,available,sold,rejected',]);

        $newStatus = $request->status;

        $this->rentalWorkflowService->updateStatus($order, $newStatus, auth()->user());

        return back()->with('success', __('Order status updated successfully'));
    }
}
