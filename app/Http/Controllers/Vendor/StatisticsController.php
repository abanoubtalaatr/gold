<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{
    Rating,
    OrderRental,
    OrderSale,
    GoldPiece,
    Branch
};

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $dateRange = $this->getDateRange(
            $request['periodType'] ?? 'weekly', // Default to weekly if not provided
            $request['startDate'] ?? null,
            $request['endDate'] ?? null
        );

        $vendorId = auth()->id();

        $statistics = [
            'totalRatings' => $this->getTotalRatings($vendorId, $dateRange),
            'averageRating' => round($this->getAverageRating($vendorId, $dateRange), 1),
            'completedReservations' => $this->getCompletedReservations($vendorId, $dateRange),
            'currentReservations' => $this->getCurrentReservations($vendorId),
            'upcomingReservations' => $this->getUpcomingReservations($vendorId),
            'availablePieces' => $this->getAvailablePieces($vendorId),
            'soldPieces' => $this->getSoldPieces($vendorId, $dateRange),
        ];

        // Return the view with statistics data
        return inertia('Vendor.Statistics.Index', [
            'statistics' => $statistics,
            'filters' => [
                'periodType' => $request['periodType'] ?? 'weekly',
                'startDate' => $request['startDate'] ?? null,
                'endDate' => $request['endDate'] ?? null,
            ]
        ]);
    }

    protected function getDateRange($periodType, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        switch ($periodType) {
            case 'daily':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'weekly':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'quarterly':
                return [
                    'start' => $now->copy()->startOfQuarter(),
                    'end' => $now->copy()->endOfQuarter()
                ];
            case 'semiannual':
                return [
                    'start' => $now->copy()->month < 7 ? $now->copy()->startOfYear() : $now->copy()->startOfYear()->addMonths(6),
                    'end' => $now->copy()->month < 7 ? $now->copy()->startOfYear()->addMonths(5)->endOfMonth() : $now->copy()->endOfYear()
                ];
            case 'annual':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            case 'custom':
                return [
                    'start' => Carbon::parse($startDate)->startOfDay(),
                    'end' => Carbon::parse($endDate)->endOfDay()
                ];
        }
    }

    protected function getTotalRatings($vendorId, $dateRange)
    {
        return Rating::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereHas('goldPiece.branch', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->count();
    }

    protected function getAverageRating($vendorId, $dateRange)
    {
        return Rating::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereHas('goldPiece.branch', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->avg('rating') ?? 0;
    }

    protected function getCompletedReservations($vendorId, $dateRange)
    {
        return OrderRental::where('status', OrderRental::STATUS_AVAILABLE)
            ->whereBetween('end_date', [$dateRange['start'], $dateRange['end']])
            ->whereHas('branch', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->count();
    }

    protected function getCurrentReservations($vendorId)
    {
        return OrderRental::where('status', OrderRental::STATUS_RENTED)
            ->whereHas('branch', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->count();
    }

    protected function getUpcomingReservations($vendorId)
    {
        return OrderRental::where('status', OrderRental::STATUS_APPROVED)
            ->where('start_date', '>', now())
            ->whereHas('branch', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->count();
    }

    protected function getAvailablePieces($vendorId)
    {
        return GoldPiece::where('status', 'available')
            ->whereHas('branch', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->count();
    }

    protected function getSoldPieces($vendorId, $dateRange)
    {
        return OrderSale::where('status', OrderSale::STATUS_SOLD)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->whereHas('branch', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->count();
    }
}