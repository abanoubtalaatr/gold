<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Notifications\Client\GoldPieceAcceptedNotification;
use App\Notifications\Client\GoldPieceRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderSalesController extends Controller
{

    public function index(Request $request)
    {
        $vendorId = $request->user()->id;
        $branchIds = Branch::where('vendor_id', $vendorId)->pluck('id');
        $branches = Branch::where('vendor_id', $vendorId)->select('id', 'name')->get();

        $filters = $request->only(['search', 'branch_id', 'status']);
        $statusFilter = $filters['status'] ?? null;

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
            ->when($statusFilter === 'pending', function ($query) {
                $query->where('status', 'pending_approval');
            })
            ->with(['user', 'goldPiece', 'branch'])
            ->orderBy('created_at', 'desc');

        $saleOrders = $saleOrdersQuery->paginate(10)->appends($filters);

        // $saleOrders->getCollection()->transform(function ($order) {
        //     if ($order->goldPiece) {
        //         $order->goldPiece->qr_code = base64_encode(
        //             QrCode::format('png')->size(100)->generate(
        //                 route('vendor.gold-pieces.show', $order->goldPiece->id)
        //             )
        //         );
        //     }
        //     return $order;
        // });

        return Inertia::render('Vendor/Orders/SaleIndex', [
            'saleOrders' => $saleOrders,
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function accept(Request $request, $orderId)
    {

        $order = OrderSale::findOrFail($orderId);
        $this->authorizeVendor($order);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $order->update([
            'branch_id' => $request->branch_id,
            'status' => OrderSale::STATUS_APPROVED,
        ]);
        // Notify gold piece owner
        if ($order->goldPiece->user) {
            $order->goldPiece->user->notify(
                new GoldPieceAcceptedNotification($order, auth()->user()->name)
            );
        }
        Log::info('Order accepted', ['order_id' => $order->id, 'vendor_id' => $request->user()->id]);

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $orderId)
    {
        $order = OrderSale::findOrFail($orderId);
        $this->authorizeVendor($order);

        $order->update(['status' => OrderSale::STATUS_REJECTED]);

        // Notify gold piece owner
        if ($order->goldPiece->user) {
            $order->goldPiece->user->notify(
                new GoldPieceRejectedNotification($order, auth()->user()->name)
            );
        }
        Log::info('Order rejected', ['order_id' => $order->id, 'vendor_id' => $request->user()->id]);

        return back()->with('success', __('Order rejected successfully'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $order = OrderSale::findOrFail($orderId);
        $this->authorizeVendor($order);

        // Validate the incoming status to be one of the allowed statuses
        $request->validate([
            'status' => 'required|in:pending_approval,approved,sold,rejected',
        ]);

        // Map the input status to the internal constants
        $newStatus = match ($request->status) {
            'pending_approval' => OrderSale::STATUS_PENDING_APPROVAL,
            'approved' => OrderSale::STATUS_APPROVED,
            'sold' => OrderSale::STATUS_SOLD,
            'rejected' => OrderSale::STATUS_REJECTED,
            default => throw new \Exception('Invalid status'),
        };

        // Update the order with the new status
        $order->update(['status' => $newStatus]);

        // Log the status update
        Log::info('Order status updated', ['order_id' => $order->id, 'status' => $newStatus]);

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