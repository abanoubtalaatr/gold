<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\OrderRental;
use App\Services\RentalWorkflowService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RentalRequestController extends Controller
{
    protected $rentalWorkflowService;

    public function __construct(RentalWorkflowService $rentalWorkflowService)
    {
        $this->rentalWorkflowService = $rentalWorkflowService;
    }

    public function index(Request $request)
    {
        $vendorId = $request->user()->id;
        $branchIds = Branch::where('vendor_id', $vendorId)->pluck('id');
        $branches = Branch::where('vendor_id', $vendorId)->select('id', 'name')->get();
        $statuses = OrderRental::statuses(); // Get dynamic statuses from OrderRental model

        // Filters
        $filters = $request->only([
            'search',
            'branch_id',
            'service_type',
            'piece_name',
            'description',
            'price_min',
            'price_max',
            'date_filter',
            'date_from',
            'date_to',
            'status',
        ]);

        
        // Query for rental orders with lease type only
        $ordersQuery = OrderRental::query()
            ->where('type', OrderRental::LEASE_TYPE)
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
            ->when($filters['service_type'] ?? null, function ($query, $serviceType) {
                $query->whereHas('goldPiece', function ($q) use ($serviceType) {
                    $q->where('type', $serviceType);
                });
            })
            ->when($filters['piece_name'] ?? null, function ($query, $pieceName) {
                $query->whereHas('goldPiece', function ($q) use ($pieceName) {
                    $q->where('name', 'like', "%{$pieceName}%");
                });
            })
            ->when($filters['description'] ?? null, function ($query, $description) {
                $query->whereHas('goldPiece', function ($q) use ($description) {
                    $q->where('description', 'like', "%{$description}%");
                });
            })
            ->when(isset($filters['price_min']) || isset($filters['price_max']), function ($query) use ($filters) {
                $query->whereBetween('total_price', [
                    $filters['price_min'] ?? 0,
                    $filters['price_max'] ?? PHP_INT_MAX,
                ]);
            })
            ->when($filters['date_filter'] ?? null, function ($query, $dateFilter) use ($filters) {
                if ($dateFilter === 'today') {
                    $query->whereDate('start_date', Carbon::today());
                } elseif ($dateFilter === 'week') {
                    $query->whereBetween('start_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                } elseif ($dateFilter === 'custom' && $filters['date_from'] && $filters['date_to']) {
                    $query->whereBetween('start_date', [
                        Carbon::parse($filters['date_from']),
                        Carbon::parse($filters['date_to']),
                    ]);
                }
            })
            ->when(isset($filters['status']) && in_array($filters['status'], OrderRental::statuses()), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
                // Date-based conditions for specific statuses
                if ($filters['status'] === OrderRental::STATUS_RENTED) {
                    $query->where('start_date', '<=', Carbon::now())
                          ->where('end_date', '>=', Carbon::now());
                } elseif ($filters['status'] === OrderRental::STATUS_APPROVED) {
                    $query->where('start_date', '>', Carbon::now());
                } elseif ($filters['status'] === OrderRental::STATUS_AVAILABLE) {
                    $query->where('end_date', '<', Carbon::now());
                }
            })
            ->with(['user', 'goldPiece', 'branch'])
            ->select([
                'id',
                'user_id',
                'gold_piece_id',
                'branch_id',
                'total_price',
                'status',
                'created_at',
                'updated_at',
                'start_date',
                'end_date',
            ]);

        // Paginate results
        $orders = $ordersQuery->paginate(10)->appends($filters);

        // Transform orders to include rental_days, invoice, and allowed_actions
        $orders->getCollection()->transform(function ($order) {
            $order->rental_days = $order->start_date && $order->end_date
                ? Carbon::parse($order->end_date)->diffInDays(Carbon::parse($order->start_date))
                : null;
            $order->invoice = $order->status === 'payment_confirmed' ? $this->generateInvoice($order) : null;
            $order->allowed_actions = $this->getAllowedActions($order);
            return $order;
        });

        return Inertia::render('Vendor/RentalRequests/Index', [
            'orders' => $orders,
            'branches' => $branches,
            'filters' => $filters,
            'statuses' => $statuses, // Only rental statuses
        ]);
    }

    protected function generateInvoice($order)
    {
        return [
            'order_id' => $order->id,
            'user_name' => $order->user->name,
            'piece_name' => $order->goldPiece->name,
            'service_type' => $order->goldPiece->type,
            'rental_days' => $order->start_date && $order->end_date
                ? Carbon::parse($order->end_date)->diffInDays(Carbon::parse($order->start_date))
                : null,
            'total_price' => $order->total_price,
            'start_date' => $order->start_date,
            'end_date' => $order->end_date,
            'branch_name' => $order->branch->name,
            'created_at' => $order->created_at->toDateTimeString(),
            'status' => 'payment_confirmed',
        ];
    }

    protected function getAllowedActions($order)
    {
        $allowedTransitions = $this->rentalWorkflowService->getAllowedTransitions($order->status);
        $actions = [];

        foreach ($allowedTransitions as $transition) {
            switch ($transition) {
                case OrderRental::STATUS_APPROVED:
                    $actions[] = 'approve';
                    break;
                case OrderRental::STATUS_REJECTED:
                    $actions[] = 'reject';
                    break;
                case OrderRental::STATUS_PIECE_SENT:
                    $actions[] = 'mark_as_sent';
                    break;
                case OrderRental::STATUS_RENTED:
                    $actions[] = 'confirm_rental';
                    break;
                case OrderRental::STATUS_AVAILABLE:
                    $actions[] = 'complete_rental';
                    break;
            }
        }

        return $actions;
    }

    public function accept(Request $request, $orderId)
    {
        try {
            $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
            $this->authorizeVendor($order);

            $request->validate([
                'branch_id' => 'required|exists:branches,id',
            ]);

            // Use the workflow service for proper status management
            $this->rentalWorkflowService->approve($order, $request->branch_id, $request->user());

            return back()->with('success', __('Order accepted successfully'));

        } catch (\Exception $e) {
            Log::error('Failed to accept rental order', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('Failed to accept order. Please try again.'));
        }
    }

    public function reject(Request $request, $orderId)
    {
        try {
            $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
            $this->authorizeVendor($order);

            // Use the workflow service for proper status management
            $this->rentalWorkflowService->reject($order, $order->user);

            return back()->with('success', __('Order rejected successfully'));

        } catch (\Exception $e) {
            Log::error('Failed to reject rental order', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('Failed to reject order. Please try again.'));
        }
    }

    public function markAsSent(Request $request, $orderId)
    {
        try {
            $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
            $this->authorizeVendor($order);

            if ($order->status !== OrderRental::STATUS_APPROVED) {
                return back()->with('error', __('Order must be approved before marking as sent.'));
            }

            // Use the workflow service for proper status management
            $this->rentalWorkflowService->markAsSent($order, $request->user());

            return back()->with('success', __('Order marked as sent successfully'));

        } catch (\Exception $e) {
            Log::error('Failed to mark rental order as sent', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('Failed to mark order as sent. Please try again.'));
        }
    }

    public function confirmRental(Request $request, $orderId)
    {
        try {
            $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
            $this->authorizeVendor($order);

            if ($order->status !== OrderRental::STATUS_PIECE_SENT) {
                return back()->with('error', __('Order must be marked as sent before confirming rental.'));
            }

            // Use the workflow service for proper status management
            $this->rentalWorkflowService->confirmRental($order, $request->user());

            return back()->with('success', __('Rental confirmed successfully'));

        } catch (\Exception $e) {
            Log::error('Failed to confirm rental', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('Failed to confirm rental. Please try again.'));
        }
    }

    public function completeRental(Request $request, $orderId)
    {
        try {
            $order = OrderRental::with(['goldPiece', 'goldPiece.user', 'user', 'branch'])->findOrFail($orderId);
            $this->authorizeVendor($order);

            if ($order->status !== OrderRental::STATUS_RENTED) {
                return back()->with('error', __('Order must be in rented status to complete.'));
            }

            // Use the workflow service for proper status management
            $this->rentalWorkflowService->completeRental($order, $request->user());

            return back()->with('success', __('Rental completed successfully'));

        } catch (\Exception $e) {
            Log::error('Failed to complete rental', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('Failed to complete rental. Please try again.'));
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