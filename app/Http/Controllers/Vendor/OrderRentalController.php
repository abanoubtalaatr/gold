<?php

namespace App\Http\Controllers\Vendor;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\Wallet;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RentalWorkflowService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
            ->with(['user', 'goldPiece.media', 'goldPiece.user', 'branch'])
            ->orderBy('created_at', 'desc');


        $rentalOrders = $rentalOrdersQuery->paginate(100)->appends($filters);

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

        // remove this order renatal form other branches
        OrderRental::where('gold_piece_id', $order->gold_piece_id)->where('id', '!=', $orderId)->delete();

        $order->update([
            'status' => OrderRental::STATUS_APPROVED,
            'branch_id'=> $request->input('branch_id'),
        ]);


        //real time for mobile app
        event(new OrderRentalStatusChangeEvent($order));
        // Notify gold piece owner
        $goldPieceUser = $order->goldPiece?->user;
        if ($goldPieceUser) {
            $goldPieceUser->notify(
                new GoldPieceAcceptedNotification($order, auth()->user()->name)
            );
        }

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $orderId)
    {
        $order = OrderRental::with(['goldPiece', 'goldPiece.user'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        $order->update(['status' => 'rented']);

        // Notify gold piece owner
        //real time for mobile app
        event(new OrderRentalStatusChangeEvent($order));

        $goldPieceUser = $order->goldPiece?->user;
        if ($goldPieceUser) {
            $goldPieceUser->notify(
                new GoldPieceRejectedNotification($order, auth()->user()->name)
            );
        }

        return back();
    }

    public function updateStatus(Request $request, $orderId)
    {
        $order = OrderRental::with(['user', 'branch', 'goldPiece'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        // Validate the incoming status
        $request->validate([
            'status' => 'required|in:pending_approval,approved,piece_sent,rented,available,sold,rejected',
        ]);

        $newStatus = $request->status;
        
        if($newStatus == OrderRental::STATUS_RENTED){
            
            $totalPrice = $order->total_price;
            $settings = SystemSetting::first();
            $merchantCommission = $settings->merchant_commission_percentage ?? 0;
            $platformCommission = $settings->platform_commission_percentage ?? 0;

            $vendorAmount = ($totalPrice * $merchantCommission) / 100;
            $platformAmount = ($totalPrice * $platformCommission) / 100;

            // i want for vendor first check if the wallet is exist or not if not create new one
            $vendorWallet = Wallet::where('user_id', auth()->id())->first();

            if (!$vendorWallet) {
                $vendorWallet = Wallet::create([
                    'user_id' => $order->user_id,
                ]);
            }
            // create wallet transaction for the vendor
            $vendorWallet->credit($vendorAmount, 'Vendor commission for order #' . $order->id);
            $vendorWallet->update(['balance' => $vendorWallet->balance + $vendorAmount,'pending_balance' => $vendorWallet->pending_balance + $vendorAmount]);

            // create wallet transaction for the platform
            $platformWallet = Wallet::where('user_id', User::where('email', 'admin@admin.com')->first()->id)->first();

            $platformWallet->credit($platformAmount, 'Platform commission for order #' . $order->id);
            $platformWallet->update(['balance' => $platformWallet->balance + $platformAmount,'total_earned' => $platformWallet->total_earned + $platformAmount]);
        }

        // Use workflow service for proper status management and notifications
        $this->rentalWorkflowService->updateStatus($order, $newStatus, Auth::user());

        //real time for mobile app
        event(new OrderRentalStatusChangeEvent($order));

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
