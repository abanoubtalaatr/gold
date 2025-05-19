<?php

namespace App\Http\Controllers\Vendor;

use Inertia\Inertia;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderSale;
use App\Models\OrderRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $vendorId = $request->user()->id;
        $branchIds = Branch::where('vendor_id', $vendorId)->pluck('id');
        $branches = Branch::where('vendor_id', $vendorId)->select('id', 'name')->get();

        // Filters
        $filters = $request->only(['search', 'branch_id', 'status']);
        $statusFilter = $filters['status'] ?? null;

        // Rental Orders Query
        $rentalOrdersQuery = OrderRental::query()->where('type',OrderRental::RENT_TYPE)
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

        // Sale Orders Query
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

        // Paginate
        $rentalOrders = $rentalOrdersQuery->paginate(10, ['*'], 'rental_page')->appends($filters);
        $saleOrders = $saleOrdersQuery->paginate(10, ['*'], 'sale_page')->appends($filters);

        // Generate QR codes for gold pieces
        $rentalOrders->getCollection()->transform(function ($order) {
            if ($order->goldPiece) {
                $order->goldPiece->qr_code = base64_encode(
                    QrCode::format('png')->size(100)->generate(
                        route('gold-piece.show', $order->goldPiece->id)
                    )
                );
            }
            return $order;
        });

        $saleOrders->getCollection()->transform(function ($order) {
            if ($order->goldPiece) {
                $order->goldPiece->qr_code = base64_encode(
                    QrCode::format('png')->size(100)->generate(
                        route('gold-piece.show', $order->goldPiece->id)
                    )
                );
            }
            return $order;
        });

        return Inertia::render('Vendor/Orders/Index', [
            'rentalOrders' => $rentalOrders,
            'saleOrders' => $saleOrders,
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

        Log::info('Order accepted', ['order_id' => $order->id, 'vendor_id' => $request->user()->id]);

        return back()->with('success', __('Order accepted successfully'));
    }

    public function reject(Request $request, $orderId)
    {
        $order = OrderRental::findOrFail($orderId);
        $this->authorizeVendor($order);

        $order->update(['status' => 'rejected']);

        Log::info('Order rejected', ['order_id' => $order->id, 'vendor_id' => $request->user()->id]);

        return back()->with('success', __('Order rejected successfully'));
    }

    public function updateStatus(Request $request, $orderId)
    {
        $order = OrderRental::findOrFail($orderId);
        $this->authorizeVendor($order);

        $request->validate([
            'status' => 'required|in:piece_sent,available,sold',
        ]);

        $newStatus = match ($request->status) {
            'piece_sent' => OrderRental::STATUS_PIECE_SENT,
            'available' => OrderRental::STATUS_AVAILABLE,
            'sold' => 'sold',
            default => throw new \Exception('Invalid status'),
        };

        $order->update(['status' => $newStatus]);

        Log::info('Order status updated', ['order_id' => $order->id, 'status' => $newStatus]);

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