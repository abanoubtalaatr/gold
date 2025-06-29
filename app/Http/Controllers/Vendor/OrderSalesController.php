<?php

namespace App\Http\Controllers\Vendor;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\SalesWorkflowService;
use App\Http\Services\TransactionsService;
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
        $user = $request->user()->vendor_id ?? $request->user()->id;

        $user = User::find($user);

        if ($user->debt >= SystemSetting::first()->vendor_debt_limit) {
            return back()->with('error', __('You should pay your debt first' . ' ' . $user->debt));
        }

        $maxActiveOrders = SystemSetting::first()->max_active_orders;

        // Get active orders count, excluding sold, rejected, canceled, with unique gold pieces for pending_approval
        $activeOrders = OrderSale::where('user_id', $user->id)
            ->whereNotIn('status', [
                OrderSale::STATUS_SOLD,
                OrderSale::STATUS_REJECTED,
                OrderSale::STATUS_CANCELED
            ])
            ->groupBy('gold_piece_id', 'status')
            ->select('gold_piece_id', 'status') // Explicitly select grouped columns
            ->havingRaw('status != ? OR COUNT(*) = 1', [OrderSale::STATUS_PENDING_APPROVAL])
            ->count();

        if ($activeOrders >= $maxActiveOrders) {
            return back()->with('error', __('You have reached the maximum number of active orders'));
        }

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

    public function markAsSold(Request $request, $orderId)
    {
        $orderId = $orderId; // Store the original ID
        $order = OrderSale::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);

        $this->authorizeVendor($order);

        event(new OrderSaleStatusChangedEvent($order));

        $order->update(['status' => OrderSale::STATUS_CONFIRM_SOLD_FROM_VENDOR]);

        // Notify gold piece owner using the new unified notification
        if ($order->goldPiece && $order->goldPiece->user) {
            $order->user->notify(new ChangeOrderStatusNotification($order, OrderSale::STATUS_CONFIRM_SOLD_FROM_VENDOR));
        }

        (new TransactionsService())->addTransactionForVendorAndPlatform($order, 'sale', $order->total_price, $order->branch->vendor_id, 'credit');

        return back()->with('success', __('Order marked as sold successfully'));
    }
    protected function authorizeVendor($order)
    {
        $vendorBranches = Branch::where('vendor_id', Auth::id())->pluck('id');
        if (!$vendorBranches->contains($order->branch_id) && $order->status !== OrderSale::STATUS_PENDING_APPROVAL) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function updatePrice(Request $request, $orderId)
    {
        $order = OrderSale::findOrFail($orderId);
        $this->authorizeVendor($order);

        $request->validate([
            'total_price' => 'required|numeric|min:0',
        ]);

        $order->update([
            'total_price' => $request->total_price,
        ]);

        // Optionally notify the user or gold piece owner
        if ($order->user) {
            $order->user->notify(
                new OrderSaleNotification(
                    $order,
                    'price_updated',
                    Auth::user()->name,
                    ['new_price' => $request->total_price]
                )
            );
        }

        return back()->with('success', __('Order price updated successfully'));
    }
}
