<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\OrderSale;
use App\Models\OrderRental;
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

        $rentalRequests = OrderRental::when($period !== 'all' || ($fromDate && $toDate), $dateFilter)->count();
        $salesOrders = OrderSale::when($period !== 'all' || ($fromDate && $toDate), $dateFilter)->count();
        $rentalOrders = OrderRental::when($period !== 'all' || ($fromDate && $toDate), $dateFilter)->count();

        return Inertia::render('Vendor/Dashboard', [
            'roles' => $roles,
            'admins' => $admins,
            'branches' => $branches,
            'rentalRequests' => $rentalRequests,
            'salesOrders' => $salesOrders,
            'rentalOrders' => $rentalOrders,
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
