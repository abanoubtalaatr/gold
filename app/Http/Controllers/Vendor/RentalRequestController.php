<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\OrderRental;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RentalRequestController extends Controller
{
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

        // Base query for rental orders (lease type)
        $rentalOrdersQuery = OrderRental::query()
            ->where('type', 'lease')
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
            
                DB::raw("'rental' as order_type"),
            ]);

        // Base query for sale orders
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
                    $query->whereDate('created_at', Carbon::today());
                } elseif ($dateFilter === 'week') {
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                } elseif ($dateFilter === 'custom' && $filters['date_from'] && $filters['date_to']) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($filters['date_from']),
                        Carbon::parse($filters['date_to']),
                    ]);
                }
            })
            ->when(isset($filters['status']) && $filters['status'] === 'sale', function ($query) {
                $query->where('status', '!=', ''); // Ensure query is valid
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
                DB::raw('NULL as start_date'),
                DB::raw('NULL as end_date'),
                
                DB::raw("'sale' as order_type"),
            ]);

        // Combine queries based on status filter
        $ordersQuery = isset($filters['status']) && $filters['status'] === 'sale'
            ? $saleOrdersQuery
            : (isset($filters['status']) && in_array($filters['status'], OrderRental::statuses())
                ? $rentalOrdersQuery
                : $rentalOrdersQuery->union($saleOrdersQuery));

        // Paginate combined results
        $orders = $ordersQuery->paginate(10)->appends($filters);

        // Transform orders to include rental_days and invoice
        $orders->getCollection()->transform(function ($order) {
            if ($order->order_type === 'rental') {
                $order->rental_days = $order->start_date && $order->end_date
                    ? Carbon::parse($order->end_date)->diffInDays(Carbon::parse($order->start_date))
                    : null;
                $order->invoice = $order->status === 'payment_confirmed' ? $this->generateInvoice($order) : null;
            } else {
                $order->rental_days = null;
                $order->invoice = $order->status === 'payment_confirmed' ? $this->generateSaleInvoice($order) : null;
            }
            return $order;
        });

        return Inertia::render('Vendor/RentalRequests/Index', [
            'orders' => $orders,
            'branches' => $branches,
            'filters' => $filters,
            'statuses' => array_merge($statuses, ['sale']), // Include 'sale' as a status option
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

    protected function generateSaleInvoice($order)
    {
        return [
            'order_id' => $order->id,
            'user_name' => $order->user->name,
            'piece_name' => $order->goldPiece->name,
            'service_type' => 'sale',
            'total_price' => $order->total_price,
            'branch_name' => $order->branch->name,
            'created_at' => $order->created_at->toDateTimeString(),
            'status' => 'payment_confirmed',
        ];
    }
}