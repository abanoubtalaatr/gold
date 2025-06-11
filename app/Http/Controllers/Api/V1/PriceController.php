<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\GoldPriceService;
use App\Http\Requests\Api\V1\PriceRequest;

class PriceController extends Controller
{
    use ApiResponseTrait;

    protected GoldPriceService $goldPriceService;

    public function __construct(GoldPriceService $goldPriceService)
    {
        $this->goldPriceService = $goldPriceService;
    }

    public function index(PriceRequest $request)
    {
        try {
            $totalPrice = $this->goldPriceService->calculateTotalPrice(
                $request->carat,
                $request->weight,
                $request->number_rental_day
            );

            return $this->successResponse([
                'total_price' => $totalPrice,
                'carat' => $request->carat,
                'weight' => $request->weight,
                'rental_days' => $request->number_rental_day
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to calculate price: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get structured gold prices with buy, sell, and rental options
     */
    public function structuredPrices()
    {
        try {
            $data = $this->goldPriceService->getStructuredGoldPrices();
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch structured prices: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get prices formatted for mobile app display
     */
    public function mobileFormattedPrices()
    {
        try {
            $data = $this->goldPriceService->getMobileFormattedPrices();
            return $this->successResponse($data);
        } catch (\Exception $e) {
            // Return fallback data with same structure when API fails
            $fallbackData = $this->getFallbackMobileFormattedPrices();
            return $this->successResponse($fallbackData);
        }
    }

    /**
     * Get fallback mobile formatted prices when API is unavailable
     */
    private function getFallbackMobileFormattedPrices(): array
    {
        return [
            'banner_info' => [
                'main_carat' => '24',
                'buy_price' => 393.39,
                'sell_price' => 418.39,
                'date' => now()->format('j F'),
                'currency' => 'SAR'
            ],
            'price_list' => [
                [
                    'carat_key' => '24',
                    'carat' => 'عيار 24',
                    'buy_price' => 393.39,
                    'sell_price' => 418.39,
                    'change_indicator' => 'same',
                    'is_featured' => true
                ],
                [
                    'carat_key' => '22',
                    'carat' => 'عيار 22',
                    'buy_price' => 354.78,
                    'sell_price' => 389.78,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '21',
                    'carat' => 'عيار 21',
                    'buy_price' => 332.97,
                    'sell_price' => 377.97,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '20',
                    'carat' => 'عيار 20',
                    'buy_price' => 311.16,
                    'sell_price' => 366.16,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '18',
                    'carat' => 'عيار 18',
                    'buy_price' => 272.54,
                    'sell_price' => 337.54,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '16',
                    'carat' => 'عيار 16',
                    'buy_price' => 233.93,
                    'sell_price' => 308.93,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '14',
                    'carat' => 'عيار 14',
                    'buy_price' => 195.31,
                    'sell_price' => 280.31,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '10',
                    'carat' => 'عيار 10',
                    'buy_price' => 123.08,
                    'sell_price' => 218.08,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ]
            ],
            'timestamp' => now()->timestamp
        ];
    }

    /**
     * Get price breakdown for specific transaction type (buy/sell/rental)
     */
    public function priceBreakdownByType(Request $request)
    {
        $request->validate([
            'carat' => 'required|in:24,22,21,20,18,16,14,10',
            'weight' => 'required|numeric|min:0.1',
            'type' => 'required|in:buy,sell,rental',
            'rental_days' => 'nullable|integer|min:1|required_if:type,rental'
        ]);

        try {
            $breakdown = $this->goldPriceService->getPriceBreakdownByType(
                $request->carat,
                $request->weight,
                $request->type,
                $request->rental_days
            );

            return $this->successResponse($breakdown);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get price breakdown: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @deprecated Use index() method instead
     */
    public function getTotalPrice(PriceRequest $request)
    {
        return $this->goldPriceService->calculateTotalPrice(
            $request->carat,
            $request->weight,
            $request->number_rental_day
        );
    }

    /**
     * Get real-time gold prices with adjustments applied
     */
    public function realTimePrice()
    {
        try {
            $adjustedData = $this->goldPriceService->getFormattedRealTimePricesWithAdjustments();
            return $this->successResponse($adjustedData);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch real-time prices: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get raw real-time prices without any adjustments
     */
    public function rawRealTimePrice()
    {
        try {
            $data = $this->goldPriceService->getFormattedRealTimePrices();
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch raw real-time prices: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get adjusted prices for all carats
     */
    public function adjustedPrices()
    {
        try {
            $data = $this->goldPriceService->getMarketDataWithAdjustments();
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch adjusted prices: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get price for specific carat with adjustments
     */
    public function priceForCarat(Request $request)
    {
        $request->validate([
            'carat' => 'required|in:24,22,21,20,18,16,14,10'
        ]);

        try {
            $price = $this->goldPriceService->getAdjustedPricePerGram($request->carat);
            
            return $this->successResponse([
                'carat' => $request->carat,
                'price_per_gram' => $price,
                'currency' => 'SAR'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch price for carat: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get detailed price breakdown
     */
    public function priceBreakdown(PriceRequest $request)
    {
        try {
            $breakdown = $this->goldPriceService->getPriceBreakdown(
                $request->carat,
                $request->weight,
                $request->number_rental_day
            );

            return $this->successResponse($breakdown);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get price breakdown: ' . $e->getMessage(), 500);
        }
    }
}
