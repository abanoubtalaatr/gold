<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Role;
use App\Models\User;
use App\Models\Rating;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\OrderSale;
use App\Models\OrderRental;
use App\Models\GoldPiece;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        
        // Get filter parameters from request
        $period = $request->input('period', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Apply date filters
        $dateFilter = function ($query) use ($period, $fromDate, $toDate) {
            if ($period !== 'all') {
                $this->applyPeriodFilter($query, $period);
            }

            if ($fromDate && $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            }
        };

        // Get counts with filters
        $roles = Role::where('vendor_id', Auth::id())->count();
        $admins = User::where('vendor_id', Auth::id())->whereHas('roles')->count();
        $branches = Branch::where('vendor_id', Auth::id())->count();

        // Sales orders count
        $salesOrdersQuery = OrderSale::query();
        $salesOrdersQuery->whereHas('branch.vendor', function ($query) {
            $query->where('id', Auth::id());
        });
        if ($period !== 'all' || ($fromDate && $toDate)) {
            $salesOrdersQuery->when($dateFilter, function ($query) use ($dateFilter) {
                $query->where($dateFilter);
            });
        }
        $salesOrders = $salesOrdersQuery->count();

        // Rental orders count
        $rentalOrdersQuery = OrderRental::query();
        $rentalOrdersQuery->whereHas('branch.vendor', function ($query) {
            $query->where('id', Auth::id());
        })->where('type',OrderRental::RENT_TYPE);

        if ($period !== 'all' || ($fromDate && $toDate)) {
            $rentalOrdersQuery->when($dateFilter, function ($query) use ($dateFilter) {
                $query->where($dateFilter);
            });
        }

        $rentalRequestQuery = OrderRental::query();
        $rentalRequestQuery->whereHas('branch.vendor', function ($query) {
            $query->where('id', Auth::id());
        })->where('type',OrderRental::LEASE_TYPE);
        
        if ($period !== 'all' || ($fromDate && $toDate)) {
            $rentalRequestQuery->when($dateFilter, function ($query) use ($dateFilter) {
                $query->where($dateFilter);
            });
        }

        $rentalOrders = $rentalOrdersQuery->count();
        $rentalRequests = $rentalRequestQuery->count();

        // Rental statistics
        $rentalStats = [
            'completed' => OrderRental::whereHas('branch.vendor', function ($query) {
                    $query->where('id', Auth::id());
                })
                ->whereIn('status', ['available', 'sold', 'rejected'])
                ->when($period !== 'all' || ($fromDate && $toDate), $dateFilter)
                ->count(),
            'current' => OrderRental::whereHas('branch.vendor', function ($query) {
                    $query->where('id', Auth::id());
                })
                ->where('status', 'rented')
                ->when($period !== 'all' || ($fromDate && $toDate), $dateFilter)
                ->count(),
            'upcoming' => OrderRental::whereHas('branch.vendor', function ($query) {
                    $query->where('id', Auth::id());
                })
                ->whereIn('status', ['pending_approval', 'approved', 'piece_sent'])
                ->when($period !== 'all' || ($fromDate && $toDate), $dateFilter)
                ->count(),
        ];

        // Gold pieces statistics
        $piecesStats = [
            'available' => GoldPiece::whereHas('branch.vendor', function ($query) {
                    $query->where('id', Auth::id());
                })
                ->where('status', 'available')
                ->when($period !== 'all' || ($fromDate && $toDate), $dateFilter)
                ->count(),
            'purchased' => OrderSale::whereHas('branch.vendor', function ($query) {
                    $query->where('id', Auth::id());
                })
                ->when($period !== 'all' || ($fromDate && $toDate), $dateFilter)
                ->count(),
        ];

        // Ratings data
        $ratings = [
            'average' => GoldPiece::whereHas('branch.vendor', function($query) {
                    $query->where('id', Auth::id());
                })
                ->withAvg('ratings as average_rating', 'rating')
                ->get()
                ->avg('average_rating') ?? 0,

            'total' => Rating::whereHas('goldPiece.branch.vendor', function($query) {
                    $query->where('id', Auth::id());
                })
                ->when($period !== 'all' || ($fromDate && $toDate), $dateFilter)
                ->count(),

            'breakdown' => Rating::whereHas('goldPiece.branch.vendor', function($query) {
                    $query->where('id', Auth::id());
                })
                ->when($period !== 'all' || ($fromDate && $toDate), $dateFilter)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->orderBy('rating', 'DESC')
                ->pluck('count', 'rating')
                ->toArray()
        ];

        // Convert breakdown to 5-element array [5-star, 4-star, ..., 1-star]
        $breakdown = array_fill(1, 5, 0);
        foreach ($ratings['breakdown'] as $rating => $count) {
            $breakdown[$rating] = $count;
        }
        $ratings['breakdown'] = array_reverse(array_values($breakdown));

        return Inertia::render('Vendor/Dashboard', [
            'roles' => $roles,
            'admins' => $admins,
            'branches' => $branches,
            'salesOrders' => $salesOrders,
            'rentalOrders' => $rentalOrders,
            'rentalRequests' => $rentalRequests,// استئجار
            'rentalStats' => $rentalStats,
            'piecesStats' => $piecesStats,
            'ratings' => $ratings,
            'filters' => [
                'period' => $period,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
        ]);
    }

    protected function applyPeriodFilter($query, $period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'daily':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [
                    $now->startOfWeek()->toDateTimeString(),
                    $now->endOfWeek()->toDateTimeString()
                ]);
                break;
            case 'monthly':
                $query->whereBetween('created_at', [
                    $now->startOfMonth()->toDateTimeString(),
                    $now->endOfMonth()->toDateTimeString()
                ]);
                break;
            case 'quarterly':
                $query->whereBetween('created_at', [
                    $now->startOfQuarter()->toDateTimeString(),
                    $now->endOfQuarter()->toDateTimeString()
                ]);
                break;
            case 'semi-annually':
                $month = $now->month <= 6 ? 1 : 7;
                $query->whereBetween('created_at', [
                    $now->month($month)->startOfMonth()->toDateTimeString(),
                    $now->month($month + 5)->endOfMonth()->toDateTimeString()
                ]);
                break;
            case 'annually':
                $query->whereBetween('created_at', [
                    $now->startOfYear()->toDateTimeString(),
                    $now->endOfYear()->toDateTimeString()
                ]);
                break;
        }
    }
}
