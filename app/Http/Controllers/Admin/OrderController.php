<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\OrderRental;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'type',
            'service_name',
            'service_description',
            'days',
            'date_from',
            'date_to',
            'price_min',
            'price_max',
            'time_range',
            'status'
        ]);

        // Rental Orders Query
        $rentalOrdersQuery = OrderRental::query()
            ->with(['user', 'goldPiece', 'branch'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('goldPiece', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($filters['service_name'] ?? null, function ($query, $name) {
                $query->whereHas('goldPiece', function ($q) use ($name) {
                    $q->where('name', 'like', "%{$name}%");
                });
            })
            ->when($filters['service_description'] ?? null, function ($query, $description) {
                $query->whereHas('goldPiece', function ($q) use ($description) {
                    $q->where('description', 'like', "%{$description}%");
                });
            })
            ->when($filters['days'] ?? null, function ($query, $days) {
                $query->where('rental_days', $days);
            })
            ->when($filters['date_from'] ?? null, function ($query, $date) {
                $query->whereDate('start_date', '>=', $date);
            })
            ->when($filters['date_to'] ?? null, function ($query, $date) {
                $query->whereDate('end_date', '<=', $date);
            })
            ->when($filters['price_min'] ?? null, function ($query, $price) {
                $query->where('total_price', '>=', $price);
            })
            ->when($filters['price_max'] ?? null, function ($query, $price) {
                $query->where('total_price', '<=', $price);
            })
            ->when($filters['time_range'] ?? null, function ($query, $range) {
                if ($range === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($range === 'week') {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                }
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if ($status === 'active') {
                    $query->whereIn('status', ['approved', 'piece_sent', 'rented']);
                } elseif ($status === 'future') {
                    $query->where('status', 'approved')
                          ->whereDate('start_date', '>', now());
                } elseif ($status === 'completed') {
                    $query->whereIn('status', ['available', 'sold'])
                          ->whereDate('end_date', '<=', now());
                }
            })
            ->orderBy('created_at', 'desc');

        // Sale Orders Query
        $saleOrdersQuery = OrderSale::query()
            ->with(['user', 'goldPiece', 'branch'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('goldPiece', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($filters['service_name'] ?? null, function ($query, $name) {
                $query->whereHas('goldPiece', function ($q) use ($name) {
                    $q->where('name', 'like', "%{$name}%");
                });
            })
            ->when($filters['service_description'] ?? null, function ($query, $description) {
                $query->whereHas('goldPiece', function ($q) use ($description) {
                    $q->where('description', 'like', "%{$description}%");
                });
            })
            ->when($filters['price_min'] ?? null, function ($query, $price) {
                $query->where('total_price', '>=', $price);
            })
            ->when($filters['price_max'] ?? null, function ($query, $price) {
                $query->where('total_price', '<=', $price);
            })
            ->when($filters['time_range'] ?? null, function ($query, $range) {
                if ($range === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($range === 'week') {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                }
            })
            ->orderBy('created_at', 'desc');

        $rentalOrders = $rentalOrdersQuery->paginate(10, ['*'], 'rental_page')->appends($filters);
        $saleOrders = $saleOrdersQuery->paginate(10, ['*'], 'sale_page')->appends($filters);
        return Inertia::render('Admin/Orders/Index', [
            'rentalOrders' => $rentalOrders,
            'saleOrders' => $saleOrders,
            'filters' => $filters,
            'statusOptions' => [
                'active' => 'Active',
                'future' => 'Future',
                'completed' => 'Completed'
            ],
            'timeRangeOptions' => [
                'today' => 'Today',
                'week' => 'This Week',
                'custom' => 'Custom Range'
            ]
        ]);
    }

    public function show($id, $type)
    {
        if ($type === 'rental') {
            $order = OrderRental::with(['user', 'goldPiece', 'branch'])->findOrFail($id);
        } else {
            $order = OrderSale::with(['user', 'goldPiece', 'branch'])->findOrFail($id);
        }

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'type' => $type
        ]);
    }
}