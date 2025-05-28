<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Review;
use App\Models\User;
// use App\Models\SalesOrder;
use App\Models\RentalRequest;
use App\Models\RentalOrder;
use App\Models\Branch;
use App\Models\OrderRental;
use App\Models\OrderSale;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters from request
        $period = $request->input('period', 'monthly');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Apply date filters
        $dateFilter = function ($query) use ($period, $start_date, $end_date) {
            if ($period !== 'all') {
                $this->applyPeriodFilter($query, $period);
            }

            if ($start_date && $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date]);
            }
        };

        // Get counts with filters
        $userCount = User::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
            ->whereIsActive(1)
            ->count();

        $rolesCount = Role::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
            ->whereIsActive(1)
            ->count();

        $reviewsData = $this->getReviewsData($period, $start_date, $end_date);

        // Get new counts with filters
        $salesOrdersCount = OrderSale::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
            ->count();

        $rentalRequestsCount = OrderRental::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
            ->count();

        $rentalOrdersCount = OrderSale::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
            ->count();

        $branchesCount = Branch::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
            ->whereIsActive(1)
            ->count();

        $vendorsCount = User::role('vendor')
            ->when($period !== 'all' || ($start_date && $end_date), $dateFilter)
            ->whereIsActive(1)
            ->count();

        // Get chart data with filters
        $UserPerRolechartData = $this->getRolesChartData($period, $start_date, $end_date);
        $statusChartData = $this->getUsersChartDataByStatus($period, $start_date, $end_date);

        return Inertia::render('Dashboard', [
            'UserPerRolechartData' => $UserPerRolechartData,
            'statusChartData' => $statusChartData,
            'reviewsData' => $reviewsData,
            'userCount' => $userCount,
            'rolesCount' => $rolesCount,
            'salesOrdersCount' => $salesOrdersCount,
            'rentalRequestsCount' => $rentalRequestsCount,
            'rentalOrdersCount' => $rentalOrdersCount,
            'branchesCount' => $branchesCount,
            'vendorsCount' => $vendorsCount,
            'filters' => [
                'period' => $period,
                'start_date' => $start_date,
                'end_date' => $end_date,
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
            case 'semiannually':
                $month = $now->month <= 6 ? 1 : 7;
                $query->whereBetween('created_at', [
                    $now->month($month)->startOfMonth()->toDateTimeString(),
                    $now->month($month + 5)->endOfMonth()->toDateTimeString()
                ]);
                break;
            case 'yearly':
                $query->whereBetween('created_at', [
                    $now->startOfYear()->toDateTimeString(),
                    $now->endOfYear()->toDateTimeString()
                ]);
                break;
        }
    }

    private function getRolesChartData($period = 'all', $start_date = null, $end_date = null)
    {
        $cacheKey = 'roles_chart_data_' . $period . '_' . $start_date . '_' . $end_date;

        return Cache::remember($cacheKey, 60, function () use ($period, $start_date, $end_date) {
            $query = Role::withCount([
                'users' => function ($query) use ($period, $start_date, $end_date) {
                    if ($period !== 'all') {
                        $this->applyPeriodFilter($query, $period);
                    }

                    if ($start_date && $end_date) {
                        $query->whereBetween('users.created_at', [$start_date, $end_date]);
                    }
                }
            ]);

            $roles = $query->get();

            return [
                'labels' => $roles->pluck('name'),
                'datasets' => [
                    [
                        'label' => 'Users per Role',
                        'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56'],
                        'data' => $roles->pluck('users_count'),
                    ],
                ],
            ];
        });
    }

    private function getUsersChartDataByStatus($period = 'all', $start_date = null, $end_date = null)
    {
        $cacheKey = 'users_by_status_chart_data_' . $period . '_' . $start_date . '_' . $end_date;

        return Cache::remember($cacheKey, 60, function () use ($period, $start_date, $end_date) {
            $query = User::select('is_active', DB::raw('count(*) as total'));

            if ($period !== 'all') {
                $this->applyPeriodFilter($query, $period);
            }

            if ($start_date && $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date]);
            }

            $usersByStatus = $query->groupBy('is_active')->get();

            return [
                'labels' => ['Inactive', 'Active'],
                'datasets' => [
                    [
                        'label' => 'Users by Status',
                        'backgroundColor' => ['#FF6384', '#36A2EB'],
                        'data' => [
                            $usersByStatus->where('is_active', 0)->first()->total ?? 0,
                            $usersByStatus->where('is_active', 1)->first()->total ?? 0,
                        ],
                    ],
                ],
            ];
        });
    }

    private function getReviewsData($period = 'all', $start_date = null, $end_date = null)
    {
        $query = Rating::query();

        if ($period !== 'all') {
            $this->applyPeriodFilter($query, $period);
        }

        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }

        $totalReviews = $query->count();
        $averageRating = $query->avg('rating');

        // Get rating distribution
        $ratingDistribution = $query->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->pluck('count', 'rating');

        // Fill missing ratings
        $fullDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $fullDistribution[$i] = $ratingDistribution[$i] ?? 0;
        }

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 1),
            'rating_distribution' => $fullDistribution,
        ];
    }
}
