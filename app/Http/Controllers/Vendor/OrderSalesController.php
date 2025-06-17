<?php

namespace App\Http\Controllers\Vendor;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\Wallet;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\SalesWorkflowService;
use App\Events\OrderSaleStatusChangedEvent;
use App\Notifications\OrderSaleNotification;
use App\Notifications\Client\ChangeOrderStatusNotification;

class OrderSalesController extends Controller
{
    protected $salesWorkflowService;

    public function __construct(SalesWorkflowService $salesWorkflowService)
    {
        $this->salesWorkflowService = $salesWorkflowService;
    }

    public function index(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $branchIds = Branch::where('vendor_id', $vendorId)->pluck('id');
        $branches = Branch::where('vendor_id', $vendorId)->select('id', 'name')->get();

        $filters = $request->only(['search', 'branch_id', 'status']);

        $saleOrdersQuery = OrderSale::query()
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
            ->with(['user', 'goldPiece.user', 'goldPiece.media', 'branch'])
            ->orderBy('created_at', 'desc');

        $saleOrders = $saleOrdersQuery->paginate(10)->appends($filters);

        // Add available actions to each order
        $saleOrders->getCollection()->transform(function ($order) {
            $order->allowed_actions = $this->salesWorkflowService->getAvailableActions($order);
            return $order;
        });

        return Inertia::render('Vendor/Orders/SaleIndex', [
            'saleOrders' => $saleOrders,
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function accept(Request $request, $orderId)
    {
        // try {
        $orderId = $orderId; // Store the original ID
        $order = OrderSale::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $branch = Branch::findOrFail($request->branch_id);

        // send notification to gold piece owner
        $order->user->notify(
            new OrderSaleNotification($order, 'accepted', Auth::user()->name, ['branch_name' => $branch->name])
        );
        // send event to update the order status
        event(new OrderSaleStatusChangedEvent($order));

        $order->update([
            'branch_id' => $branch->id,
            'status' => OrderSale::STATUS_APPROVED,
        ]);
        // delete from order sales except for this accepted branch
        OrderSale::where('id', '!=', $orderId)->where('gold_piece_id', $order->gold_piece_id)->delete();

        // Notify gold piece owner using the new unified notification
        if ($order->goldPiece && $order->goldPiece->user) {
            $order->goldPiece->user->notify(
                new OrderSaleNotification(
                    $order,
                    'accepted',
                    Auth::user()->name,
                    ['branch_name' => $branch->name]
                )
            );
        }

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $orderId)
    {
        $orderId = $orderId; // Store the original ID
        $order = OrderSale::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);

        $this->authorizeVendor($order);

        $order->update(['status' => OrderSale::STATUS_REJECTED]);

        event(new OrderSaleStatusChangedEvent($order));
        $order->user->notify(new ChangeOrderStatusNotification($order, OrderSale::STATUS_REJECTED));
        if ($order->goldPiece && $order->goldPiece->user) {
            $order->goldPiece->user->notify(
                new OrderSaleNotification(
                    $order,
                    'rejected',
                    Auth::user()->name
                )
            );
        }

        return back()->with('success', __('Order rejected successfully'));
    }

    public function markAsSent(Request $request, $orderId)
    {
        $orderId = $orderId; // Store the original ID
        $order = OrderSale::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        if ($order->status !== OrderSale::STATUS_APPROVED) {
            return back()->with('error', __('Order must be approved before marking as sent.'));
        }

        event(new OrderSaleStatusChangedEvent($order));
        
        $order->update(['status' => OrderSale::STATUS_PIECE_SENT]);
        // Notify gold piece owner using the new unified notification
        if ($order->goldPiece && $order->goldPiece->user) {
            $order->goldPiece->user->notify(
                new OrderSaleNotification(
                    $order,
                    'piece_sent',
                    Auth::user()->name
                )
            );
        }

        return back()->with('success', __('Order marked as sent successfully'));
    }

    public function markAsSold(Request $request, $orderId)
    {
        $orderId = $orderId; // Store the original ID
        $order = OrderSale::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        if ($order->status !== OrderSale::STATUS_PIECE_SENT) {
            return back()->with('error', __('Order must be marked as sent before marking as sold.'));
        }

        event(new OrderSaleStatusChangedEvent($order));

        $order->update(['status' => OrderSale::STATUS_SOLD]);

        // Notify gold piece owner using the new unified notification
        if ($order->goldPiece && $order->goldPiece->user) {
            $order->user->notify(new ChangeOrderStatusNotification($order, OrderSale::STATUS_SOLD));
        }

        // Get commission details for success message
        $totalPrice = $order->total_price;
        $settings = SystemSetting::first();
        $merchantCommission = $settings->merchant_commission_percentage ?? 0;
        $platformCommission = $settings->platform_commission_percentage ?? 0;

        $vendorAmount = ($totalPrice * $merchantCommission) / 100;
        $platformAmount = ($totalPrice * $platformCommission) / 100;

        $successMessage = __(
            'Order marked as sold successfully! Vendor commission: :vendor_amount SAR (:vendor_percent%), Platform commission: :platform_amount SAR (:platform_percent%)',
            [
                'vendor_amount' => number_format($vendorAmount, 2),
                'vendor_percent' => $merchantCommission,
                'platform_amount' => number_format($platformAmount, 2),
                'platform_percent' => $platformCommission
            ]
        );

        $vendorWallet = Wallet::where('user_id', Auth::id())->first();

        if (!$vendorWallet) {
            $vendorWallet = Wallet::create([
                'user_id' => $order->user_id,
            ]);
        }

        $vendorWallet->credit($vendorAmount, 'Vendor commission for order #' . $order->id);
        $vendorWallet->update(['balance' => $vendorWallet->balance + $vendorAmount, 'pending_balance' => $vendorWallet->pending_balance + $vendorAmount]);

        // create wallet transaction for the platform
        $platformWallet = Wallet::where('user_id', User::where('email', 'admin@admin.com')->first()->id)->first();

        $platformWallet->credit($platformAmount, 'Platform commission for order #' . $order->id);
        $platformWallet->update(['balance' => $platformWallet->balance + $platformAmount, 'pending_balance' => $platformWallet->pending_balance + $platformAmount]);

        return back()->with('success', $successMessage);
    }

    public function markAsTaken(Request $request, $orderId)
    {
        $orderId = $orderId; // Store the original ID
        $order = OrderSale::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
        $this->authorizeVendor($order);

        if ($order->status !== OrderSale::STATUS_APPROVED) {
            return back()->with('error', __('Order must be approved before marking as taken.'));
        }

        event(new OrderSaleStatusChangedEvent($order));
        
        $order->update(['status' => 'vendor_already_take_the_piece']);
        
        // Notify gold piece owner using the new unified notification
        if ($order->goldPiece && $order->goldPiece->user) {
            $order->goldPiece->user->notify(
                new OrderSaleNotification(
                    $order,
                    'vendor_already_take_the_piece',
                    Auth::user()->name
                )
            );
        }

        return back()->with('success', __('Order marked as taken successfully'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $order = OrderSale::findOrFail($orderId);
        $this->authorizeVendor($order);

        // Validate the incoming status to be one of the allowed statuses for OrderSale
        $request->validate([
            'status' => 'required|in:pending_approval,approved,piece_sent,sold,rejected,vendor_already_take_the_piece',
        ]);

        event(new OrderSaleStatusChangedEvent($order));

        $order->user->notify(new ChangeOrderStatusNotification($order, $request->status));
        
        $order->update(['status' => $request->status]);

        return back()->with('success', __('Order status updated successfully'));
    }

    protected function authorizeVendor($order)
    {
        $vendorBranches = Branch::where('vendor_id', Auth::id())->pluck('id');
        if (!$vendorBranches->contains($order->branch_id) && $order->status !== OrderSale::STATUS_PENDING_APPROVAL) {
            abort(403, 'Unauthorized action.');
        }
    }
}
