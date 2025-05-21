<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Notifications\Client\GoldPieceAcceptedNotification;
use App\Notifications\Client\GoldPieceRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderRentalController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = $request->user()->id;
        $branchIds = Branch::where('vendor_id', $vendorId)->pluck('id');
        $branches = Branch::where('vendor_id', $vendorId)->select('id', 'name')->get();

        $filters = $request->only(['search', 'branch_id', 'status']);
        $statusFilter = $filters['status'] ?? null;

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
            ->when($statusFilter === 'pending', function ($query) {
                $query->where('status', OrderRental::STATUS_PENDING_APPROVAL);
            })
            ->when($statusFilter === 'available', function ($query) {
                $query->whereIn('status', [OrderRental::STATUS_AVAILABLE, OrderRental::STATUS_RENTED]);
            })
            ->with(['user', 'goldPiece', 'branch'])
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

        $order = OrderRental::findOrFail($orderId);
        $this->authorizeVendor($order);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $order->update([
            'branch_id' => $request->branch_id,
            'status' => OrderRental::STATUS_APPROVED,
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
        $order = OrderRental::findOrFail($orderId);
        $this->authorizeVendor($order);

        $order->update(['status' => 'rejected']);

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
        $order = OrderRental::findOrFail($orderId);
        $this->authorizeVendor($order);

        $request->validate([
            'status' => 'required|in:piece_sent,available,sold,rented,pending-approval,approved',
        ]);

        $newStatus = match ($request->status) {
            'piece_sent' => OrderRental::STATUS_PIECE_SENT,
            'available' => OrderRental::STATUS_AVAILABLE,
            'sold' => OrderRental::STATUS_SOLD,
            'rented' => OrderRental::STATUS_RENTED,
            'pending-approval' => OrderRental::STATUS_PENDING_APPROVAL,
            'approved' => OrderRental::STATUS_APPROVED,

            default => throw new \Exception('Invalid status'),
        };

        $order->update(['status' => $newStatus]);


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