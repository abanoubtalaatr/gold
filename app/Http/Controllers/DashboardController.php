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
use App\Models\GoldPiece;
use App\Services\GoldPriceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    protected $goldPriceService;

    public function __construct(GoldPriceService $goldPriceService)
    {
        $this->goldPriceService = $goldPriceService;
    }

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

        // Gold Trading Specific Metrics
        $goldMetrics = $this->getGoldTradingMetrics($period, $start_date, $end_date);
        $revenueData = $this->getRevenueData($period, $start_date, $end_date);
        $popularGoldPieces = $this->getPopularGoldPieces($period, $start_date, $end_date);
        $recentTransactions = $this->getRecentTransactions();
        $goldPricesSummary = $this->getCurrentGoldPrices();

        // Get chart data with filters
        $UserPerRolechartData = $this->getRolesChartData($period, $start_date, $end_date);
        $statusChartData = $this->getUsersChartDataByStatus($period, $start_date, $end_date);
        $orderStatusChart = $this->getOrderStatusChartData($period, $start_date, $end_date);
        $monthlyRevenueChart = $this->getMonthlyRevenueChart($period, $start_date, $end_date);

        return Inertia::render('Dashboard', [
            'UserPerRolechartData' => $UserPerRolechartData,
            'statusChartData' => $statusChartData,
            'orderStatusChart' => $orderStatusChart,
            'monthlyRevenueChart' => $monthlyRevenueChart,
            'reviewsData' => $reviewsData,
            'userCount' => $userCount,
            'rolesCount' => $rolesCount,
            'salesOrdersCount' => $salesOrdersCount,
            'rentalRequestsCount' => $rentalRequestsCount,
            'rentalOrdersCount' => $rentalOrdersCount,
            'branchesCount' => $branchesCount,
            'vendorsCount' => $vendorsCount,
            'goldMetrics' => $goldMetrics,
            'revenueData' => $revenueData,
            'popularGoldPieces' => $popularGoldPieces,
            'recentTransactions' => $recentTransactions,
            'goldPricesSummary' => $goldPricesSummary,
            'filters' => [
                'period' => $period,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
        ]);
    }

    /**
     * Get gold trading specific metrics
     */
    private function getGoldTradingMetrics($period, $start_date, $end_date)
    {
        $cacheKey = 'gold_metrics_' . $period . '_' . $start_date . '_' . $end_date;

        return Cache::remember($cacheKey, 30, function () use ($period, $start_date, $end_date) {
            $dateFilter = function ($query) use ($period, $start_date, $end_date) {
                if ($period !== 'all') {
                    $this->applyPeriodFilter($query, $period);
                }
                if ($start_date && $end_date) {
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                }
            };

            $totalGoldPieces = GoldPiece::count();

            $averageOrderValue = OrderSale::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
                ->where('status', 'completed')
                ->avg('total_price') ?: 0;

            $totalRevenue = OrderSale::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
                ->where('status', 'completed')
                ->sum('total_price') ?: 0;

            $pendingOrders = OrderSale::where('status', 'pending')->count() +
                OrderRental::where('status', 'pending')->count();

            $completedOrders = OrderSale::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
                ->where('status', 'completed')->count() +
                OrderRental::when($period !== 'all' || ($start_date && $end_date), $dateFilter)
                    ->where('status', 'completed')->count();

            return [
                'total_gold_pieces' => $totalGoldPieces,
                'average_order_value' => round($averageOrderValue, 2),
                'total_revenue' => round($totalRevenue, 2),
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
            ];
        });
    }

    /**
     * Get revenue data over time
     */
    private function getRevenueData($period, $start_date, $end_date)
    {
        $cacheKey = 'revenue_data_' . $period . '_' . $start_date . '_' . $end_date;

        return Cache::remember($cacheKey, 30, function () use ($period, $start_date, $end_date) {
            $query = OrderSale::where('status', 'completed');

            if ($period !== 'all') {
                $this->applyPeriodFilter($query, $period);
            }
            if ($start_date && $end_date) {
                $query->whereBetween('created_at', [$start_date, $end_date]);
            }

            $currentRevenue = $query->sum('total_price') ?: 0;

            // Calculate previous period for comparison
            $previousQuery = OrderSale::where('status', 'completed');
            if ($period === 'monthly') {
                $previousQuery->whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ]);
            } elseif ($period === 'weekly') {
                $previousQuery->whereBetween('created_at', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ]);
            } else {
                $previousQuery->whereBetween('created_at', [
                    Carbon::now()->subDays(30),
                    Carbon::now()->subDays(60)
                ]);
            }

            $previousRevenue = $previousQuery->sum( 'total_price') ?: 0;
            $growthRate = $previousRevenue > 0 ?
                (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;

            return [
                'current_revenue' => round($currentRevenue, 2),
                'previous_revenue' => round($previousRevenue, 2),
                'growth_rate' => round($growthRate, 2),
            ];
        });
    }

    /**
     * Get popular gold pieces
     */
    private function getPopularGoldPieces($period, $start_date, $end_date)
    {
        $cacheKey = 'popular_gold_pieces_' . $period . '_' . $start_date . '_' . $end_date;

        return Cache::remember($cacheKey, 60, function () use ($period, $start_date, $end_date) {
            $query = DB::table('order_sales as os')
                ->join('gold_pieces as gp', 'os.gold_piece_id', '=', 'gp.id')
                ->select('gp.name', 'gp.carat', 'gp.weight', DB::raw('COUNT(os.id) as order_count'))
                ->where('os.status', 'completed')
                ->groupBy('gp.id', 'gp.name', 'gp.carat', 'gp.weight')
                ->orderBy('order_count', 'desc')
                ->limit(5);

            if ($period !== 'all') {
                $now = Carbon::now();
                switch ($period) {
                    case 'daily':
                        $query->whereDate('os.created_at', $now->toDateString());
                        break;
                    case 'weekly':
                        $query->whereBetween('os.created_at', [
                            $now->startOfWeek(),
                            $now->endOfWeek()
                        ]);
                        break;
                    case 'monthly':
                        $query->whereBetween('os.created_at', [
                            $now->startOfMonth(),
                            $now->endOfMonth()
                        ]);
                        break;
                }
            }

            if ($start_date && $end_date) {
                $query->whereBetween('os.created_at', [$start_date, $end_date]);
            }

            return $query->get();
        });
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions()
    {
        return Cache::remember('recent_transactions', 10, function () {
            $salesOrders = OrderSale::with(['user', 'goldPiece'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'type' => 'sale',
                        'user_name' => $order->user->name ?? 'N/A',
                        'gold_piece' => $order->goldPiece->name ?? 'N/A',
                        'amount' => $order->total_price,
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('M d, Y H:i'),
                    ];
                });

            $rentalOrders = OrderRental::with(['user'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'type' => 'rental',
                        'user_name' => $order->user->name ?? 'N/A',
                        'gold_piece' => 'Rental Item',
                        'amount' => $order->total_price ?? 0,
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('M d, Y H:i'),
                    ];
                });

            return $salesOrders->concat($rentalOrders)
                ->sortByDesc('created_at')
                ->take(10)
                ->values();
        });
    }

    /**
     * Get current gold prices summary
     */
    private function getCurrentGoldPrices()
    {
        try {
            return $this->goldPriceService->getMobileFormattedPrices();
        } catch (\Exception $e) {
            return [
                'banner_info' => [
                    'main_carat' => '24',
                    'buy_price' => 0,
                    'sell_price' => 0,
                    'date' => now()->format('j F'),
                    'currency' => 'SAR',
                ],
                'price_list' => [],
                'api_available' => false
            ];
        }
    }

    /**
     * Get order status chart data
     */
    private function getOrderStatusChartData($period, $start_date, $end_date)
    {
        $cacheKey = 'order_status_chart_' . $period . '_' . $start_date . '_' . $end_date;

        return Cache::remember($cacheKey, 30, function () use ($period, $start_date, $end_date) {
            $salesQuery = OrderSale::select('status', DB::raw('count(*) as total'));
            $rentalQuery = OrderRental::select('status', DB::raw('count(*) as total'));

            if ($period !== 'all') {
                $this->applyPeriodFilter($salesQuery, $period);
                $this->applyPeriodFilter($rentalQuery, $period);
            }

            if ($start_date && $end_date) {
                $salesQuery->whereBetween('created_at', [$start_date, $end_date]);
                $rentalQuery->whereBetween('created_at', [$start_date, $end_date]);
            }

            $salesByStatus = $salesQuery->groupBy('status')->get();
            $rentalsByStatus = $rentalQuery->groupBy('status')->get();

            $allStatuses = ['pending', 'processing', 'completed', 'cancelled', 'rejected'];
            $statusData = [];

            foreach ($allStatuses as $status) {
                $salesCount = $salesByStatus->where('status', $status)->first()->total ?? 0;
                $rentalCount = $rentalsByStatus->where('status', $status)->first()->total ?? 0;
                $statusData[$status] = $salesCount + $rentalCount;
            }

            return [
                'labels' => array_map('ucfirst', $allStatuses),
                'datasets' => [
                    [
                        'label' => 'Orders by Status',
                        'backgroundColor' => ['#FF6384', '#36A2EB', '#4BC0C0', '#FFCE56', '#FF9F40'],
                        'data' => array_values($statusData),
                    ],
                ],
            ];
        });
    }

    /**
     * Get monthly revenue chart
     */
    private function getMonthlyRevenueChart($period, $start_date, $end_date)
    {
        $cacheKey = 'monthly_revenue_chart_' . $period . '_' . $start_date . '_' . $end_date;

        return Cache::remember($cacheKey, 60, function () {
            $last6Months = [];
            $revenueData = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $last6Months[] = $month->format('M Y');

                $revenue = OrderSale::where('status', 'completed')
                    ->whereBetween('created_at', [
                        $month->startOfMonth()->copy(),
                        $month->endOfMonth()->copy()
                    ])
                    ->sum('total_price') ?: 0;

                $revenueData[] = round($revenue, 2);
            }

            return [
                'labels' => $last6Months,
                'datasets' => [
                    [
                        'label' => 'Monthly Revenue (SAR)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'data' => $revenueData,
                        'fill' => true,
                    ],
                ],
            ];
        });
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
