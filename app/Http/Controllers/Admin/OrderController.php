<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderRental;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        
        return Inertia::render('Admin/Orders/Index');
    }

    public function rentalIndex(Request $request)
    {
        $filters = $request->only([
            'search',
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

        $query = OrderRental::query()
            ->with(['user', 'goldPiece', 'branch'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('goldPiece', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
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
            ->when($filters['time_range'] ?? null, function ($query, $range) use ($filters) {
                if ($range === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($range === 'week') {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($range === 'custom' && isset($filters['date_from']) && isset($filters['date_to'])) {
                    $query->whereBetween('created_at', [
                        \Carbon\Carbon::parse($filters['date_from'])->startOfDay(),
                        \Carbon\Carbon::parse($filters['date_to'])->endOfDay()
                    ]);
                }
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if ($status === 'current') {
                    $query->where('status', 'rented');
                } elseif ($status === 'future') {
                    $query->whereIn('status', ['pending_approval', 'approved', 'piece_sent']);
                } elseif ($status === 'finished') {
                    $query->whereIn('status', ['available', 'sold', 'rejected']);
                }
            })
            ->orderBy('created_at', 'desc');

        $orders = $query->paginate(10)->appends($filters);
        return Inertia::render('Admin/Orders/RentalIndex', [
            'orders' => $orders,
            'filters' => $filters,
            'statusOptions' => [
                'current' => __('Current'),
                'future' => __('Future'),
                'finished' => __('Finished')
            ],
            'timeRangeOptions' => [
                'today' => __('Today'),
                'week' => __('This Week'),
                'custom' => __('Custom Range')
            ]
        ]);
    }

    public function saleIndex(Request $request)
    {
        $filters = $request->only([
            'search',
            'service_name',
            'service_description',
            'date_from',
            'date_to',
            'price_min',
            'price_max',
            'time_range'
        ]);

        $query = OrderSale::query()
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
            ->when($filters['time_range'] ?? null, function ($query, $range) use ($filters) {
                if ($range === 'today') {
                    $query->whereDate('created_at', today());
                } elseif ($range === 'week') {
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($range === 'custom' && isset($filters['date_from']) && isset($filters['date_to'])) {
                    $query->whereBetween('created_at', [
                        \Carbon\Carbon::parse($filters['date_from'])->startOfDay(),
                        \Carbon\Carbon::parse($filters['date_to'])->endOfDay()
                    ]);
                }
            })
            ->orderBy('created_at', 'desc');

        $orders = $query->paginate(10)->appends($filters);

        return Inertia::render('Admin/Orders/SaleIndex', [
            'orders' => $orders,
            'filters' => $filters,
            'timeRangeOptions' => [
                'today' => __('Today'),
                'week' => __('This Week'),
                'custom' => __('Custom Range')
            ]
        ]);
    }

    public function showRental($id)
    {
        dd('slss');
        $order = OrderRental::with(['user', 'goldPiece', 'branch'])->findOrFail($id);
        return Inertia::render('Admin/Orders/RentalShow', [
            'order' => $order
        ]);
    }

    public function show($id)
    {
        $order = OrderRental::with(['user', 'goldPiece', 'branch'])->findOrFail($id);
        return Inertia::render('Admin/Orders/Show', [
            'order' => $order
        ]);
    }

    public function showSale($id)
    {
        $order = OrderSale::with(['user', 'goldPiece', 'branch'])->findOrFail($id);
        return Inertia::render('Admin/Orders/SaleShow', [
            'order' => $order
        ]);
    }
}
