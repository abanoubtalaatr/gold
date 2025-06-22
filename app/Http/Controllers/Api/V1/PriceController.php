<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Traits\ApiResponseTrait;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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


            $systemSetting = SystemSetting::first();
            $deposit = $systemSetting->booking_insurance_amount??10;
            return $this->successResponse([
                'total_price' => round($totalPrice, 2),
                'carat' => $request->carat,
                'weight' => $request->weight,
                'rental_days' => $request->number_rental_day,
                'deposit' => $deposit,
            ]);
        } catch (\Exception $e) {
            $fallbackTotalPrice = $this->calculateFallbackTotalPrice(
                $request->carat,
                $request->weight,
                $request->number_rental_day,
            );
            
            return $this->successResponse([
                'total_price' => round($fallbackTotalPrice, 2),
                'carat' => $request->carat,
                'weight' => $request->weight,
                'rental_days' => $request->number_rental_day,
                'deposit' => 100,
                'current_price_for_24' => 2400,
            ]);
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
            // Log the error for debugging
            Log::warning('Gold API unavailable for structured prices', [
                'error' => $e->getMessage(),
                'endpoint' => 'structuredPrices'
            ]);
            
            // Return fallback structured data
            $fallbackData = $this->getFallbackStructuredPrices();
            return $this->successResponse($fallbackData);
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
                'buy_price' => 400,
                'sell_price' => 410,
                'date' => now()->format('j F'),
                'currency' => 'SAR'
            ],
            'price_list' => [
                [
                    'carat_key' => '24',
                    'carat' => 'عيار 24',
                    'buy_price' => 400,
                    'sell_price' => 410,
                    'change_indicator' => 'same',
                    'is_featured' => true
                ],
                [
                    'carat_key' => '22',
                    'carat' => 'عيار 22',
                    'buy_price' => 360,
                    'sell_price' => 370,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '21',
                    'carat' => 'عيار 21',
                    'buy_price' => 350,
                    'sell_price' => 360,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '20',
                    'carat' => 'عيار 20',
                    'buy_price' => 330,
                    'sell_price' => 340,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '18',
                    'carat' => 'عيار 18',
                    'buy_price' => 300,
                    'sell_price' => 310,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '16',
                    'carat' => 'عيار 16',
                    'buy_price' => 260,
                    'sell_price' => 270,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '14',
                    'carat' => 'عيار 14',
                    'buy_price' => 230,
                    'sell_price' => 240,
                    'change_indicator' => 'same',
                    'is_featured' => false
                ],
                [
                    'carat_key' => '10',
                    'carat' => 'عيار 10',
                    'buy_price' => 190,
                    'sell_price' => 200,
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
            // Log the error for debugging
            Log::warning('Gold API unavailable for price breakdown by type', [
                'error' => $e->getMessage(),
                'endpoint' => 'priceBreakdownByType',
                'carat' => $request->carat,
                'weight' => $request->weight,
                'type' => $request->type,
                'rental_days' => $request->rental_days
            ]);
            
            // Return fallback price breakdown by type
            $fallbackBreakdown = $this->getFallbackPriceBreakdownByType(
                $request->carat,
                $request->weight,
                $request->type,
                $request->rental_days
            );
            return $this->successResponse($fallbackBreakdown);
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
            // Log the error for debugging
            Log::warning('Gold API unavailable for real-time prices', [
                'error' => $e->getMessage(),
                'endpoint' => 'realTimePrice'
            ]);
            
            // Return fallback real-time data
            $fallbackData = $this->getFallbackRealTimePrices();
            return $this->successResponse($fallbackData);
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
            // Log the error for debugging
            Log::warning('Gold API unavailable for raw real-time prices', [
                'error' => $e->getMessage(),
                'endpoint' => 'rawRealTimePrice'
            ]);
            
            // Return fallback raw data
            $fallbackData = $this->getFallbackRawRealTimePrices();
            return $this->successResponse($fallbackData);
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
            // Log the error for debugging
            Log::warning('Gold API unavailable for adjusted prices', [
                'error' => $e->getMessage(),
                'endpoint' => 'adjustedPrices'
            ]);
            
            // Return fallback adjusted data
            $fallbackData = $this->getFallbackAdjustedPrices();
            return $this->successResponse($fallbackData);
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
            // Log the error for debugging
            Log::warning('Gold API unavailable for price per carat', [
                'error' => $e->getMessage(),
                'endpoint' => 'priceForCarat',
                'carat' => $request->carat
            ]);
            
            // Return fallback price for the requested carat
            $fallbackPrice = $this->getFallbackPriceForCarat($request->carat);
            return $this->successResponse([
                'carat' => $request->carat,
                'price_per_gram' => $fallbackPrice,
                'currency' => 'SAR'
            ]);
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
            // Log the error for debugging
            Log::warning('Gold API unavailable for price breakdown', [
                'error' => $e->getMessage(),
                'endpoint' => 'priceBreakdown',
                'carat' => $request->carat,
                'weight' => $request->weight,
                'rental_days' => $request->number_rental_day
            ]);
            
            // Return fallback price breakdown
            $fallbackBreakdown = $this->getFallbackPriceBreakdown(
                $request->carat,
                $request->weight,
                $request->number_rental_day
            );
            return $this->successResponse($fallbackBreakdown);
        }
    }

    /**
     * Get fallback structured prices when API is unavailable
     */
    private function getFallbackStructuredPrices(): array
    {
        return [
            'timestamp' => now()->toISOString(),
            'currency' => 'SAR',
            'prices' => [
                '24' => [
                    'carat' => '24',
                    'original_price' => 400,
                    'buy_price' => 390,
                    'sell_price' => 410,
                    'rental_price_per_day' => 4.03,
                    'buy_adjustment' => -10,
                    'sell_adjustment' => 15,
                    'price_change_indicator' => 'same',
                ],
                '22' => [
                    'carat' => '22',
                    'original_price' => 360,
                    'buy_price' => 350,
                    'sell_price' => 370,
                    'rental_price_per_day' => 3.70,
                    'buy_adjustment' => -15,
                    'sell_adjustment' => 20,
                    'price_change_indicator' => 'same',
                ],
                '21' => [
                    'carat' => '21',
                    'original_price' => 350,
                    'buy_price' => 330,
                    'sell_price' => 370,
                    'rental_price_per_day' => 3.53,
                    'buy_adjustment' => -20,
                    'sell_adjustment' => 25,
                    'price_change_indicator' => 'same',
                ],
                '20' => [
                    'carat' => '20',
                    'original_price' => 330,
                    'buy_price' => 310,
                    'sell_price' => 360,
                    'rental_price_per_day' => 3.30,
                    'buy_adjustment' => -25,
                    'sell_adjustment' => 30,
                    'price_change_indicator' => 'same',
                ],
                '18' => [
                    'carat' => '18',
                    'original_price' => 300,
                    'buy_price' => 270,
                    'sell_price' => 330,
                    'rental_price_per_day' => 3.00,
                    'buy_adjustment' => -30,
                    'sell_adjustment' => 35,
                    'price_change_indicator' => 'same',
                ],
                '16' => [
                    'carat' => '16',
                    'original_price' => 260,
                    'buy_price' => 230,
                    'sell_price' => 300,
                    'rental_price_per_day' => 2.60,
                    'buy_adjustment' => -35,
                    'sell_adjustment' => 40,
                    'price_change_indicator' => 'same',
                ],
                '14' => [
                    'carat' => '14',
                    'original_price' => 230,
                    'buy_price' => 190,
                    'sell_price' => 280,
                    'rental_price_per_day' => 2.30,
                    'buy_adjustment' => -40,
                    'sell_adjustment' => 45,
                    'price_change_indicator' => 'same',
                ],
                '10' => [
                    'carat' => '10',
                    'original_price' => 160,
                    'buy_price' => 120,
                    'sell_price' => 210,
                    'rental_price_per_day' => 1.60,
                    'buy_adjustment' => -45,
                    'sell_adjustment' => 50,
                    'price_change_indicator' => 'same',
                ],
            ],
            'market_info' => [
                'metal' => 'XAU',
                'exchange' => 'LBMA',
                'symbol' => 'XAU/SAR',
            ]
        ];
    }

    /**
     * Get fallback real-time prices when API is unavailable
     */
    private function getFallbackRealTimePrices(): array
    {
        return [
            'timestamp' => now()->timestamp,
            'metal' => 'XAU',
            'currency' => 'SAR',
            'exchange' => 'LBMA',
            'symbol' => 'XAU/SAR',
            'ask' => 410,
            'bid' => 400,
            'price' => 405,
            'ch' => 0.00,
            'price_gram_24k' => 410,
            'price_gram_22k' => 370,
            'price_gram_21k' => 360,
            'price_gram_20k' => 340,
            'price_gram_18k' => 310,
            'price_gram_16k' => 270,
            'price_gram_14k' => 240,
            'price_gram_10k' => 200,
        ];
    }

    /**
     * Get fallback raw real-time prices when API is unavailable
     */
    private function getFallbackRawRealTimePrices(): array
    {
        return [
            'timestamp' => now()->timestamp,
            'metal' => 'XAU',
            'currency' => 'SAR',
            'exchange' => 'LBMA',
            'symbol' => 'XAU/SAR',
            'ask' => 410,
            'bid' => 400,
            'price' => 405,
            'ch' => 0.00,
            'price_gram_24k' => 410,
            'price_gram_22k' => 370,
            'price_gram_21k' => 360,
            'price_gram_20k' => 340,
            'price_gram_18k' => 310,
            'price_gram_16k' => 270,
            'price_gram_14k' => 240,
            'price_gram_10k' => 200,
        ];
    }

    /**
     * Get fallback adjusted prices when API is unavailable
     */
    private function getFallbackAdjustedPrices(): array
    {
        return [
            'timestamp' => now()->timestamp,
            'metal' => 'XAU',
            'currency' => 'SAR',
            'exchange' => 'LBMA',
            'symbol' => 'XAU/SAR',
            'original_prices' => [
                'price_gram_24k' => 410,
                'price_gram_22k' => 370,
                'price_gram_21k' => 360,
                'price_gram_20k' => 340,
                'price_gram_18k' => 310,
                'price_gram_16k' => 270,
                'price_gram_14k' => 240,
                'price_gram_10k' => 200,
            ],
            'adjusted_prices' => [
                '24' => 410,
                '22' => 370,
                '21' => 360,
                '20' => 340,
                '18' => 310,
                '16' => 270,
                '14' => 240,
                '10' => 200,
            ],
            'adjustments' => [
                '24' => 5,
                '22' => 5,
                '21' => 10,
                '20' => 15,
                '18' => 20,
                '16' => 25,
                '14' => 30,
                '10' => 35,
            ]
        ];
    }

    /**
     * Get fallback price for specific carat
     */
    private function getFallbackPriceForCarat(string $carat): float
    {
        $fallbackPrices = [
            '24' => 410,
            '22' => 370,
            '21' => 360,
            '20' => 340,
            '18' => 310,
            '16' => 270,
            '14' => 240,
            '10' => 200,
        ];

        return $fallbackPrices[$carat] ?? 0;
    }

    /**
     * Get fallback price breakdown
     */
    private function getFallbackPriceBreakdown(string $carat, float $weight, ?int $rentalDays = null): array
    {
        $fallbackBasePrices = [
            '24' => 410,
            '22' => 370,
            '21' => 360,
            '20' => 340,
            '18' => 310,
            '16' => 270,
            '14' => 240,
            '10' => 1000,
        ];

        $adjustments = [
            '24' => 5,
            '22' => 5,
            '21' => 10,
            '20' => 15,
            '18' => 20,
            '16' => 25,
            '14' => 30,
            '10' => 35,
        ];

        $basePrice = $fallbackBasePrices[$carat] ?? 0;
        $adjustment = $adjustments[$carat] ?? 0;
        $adjustedPricePerGram = $basePrice + $adjustment;
        $subtotal = round($adjustedPricePerGram * $weight, 2);
        
        $breakdown = [
            'carat' => $carat,
            'weight' => $weight,
            'base_price_per_gram' => $basePrice,
            'adjustment_per_gram' => $adjustment,
            'final_price_per_gram' => $adjustedPricePerGram,
            'subtotal' => $subtotal,
            'rental_days' => $rentalDays,
            'rental_cost' => 0,
            'total' => $subtotal
        ];

        if ($rentalDays) {
            $dailyRate = 0.01; // 1% per day
            $rentalCostPerDay = $adjustedPricePerGram * $dailyRate;
            $rentalCost = round($rentalCostPerDay * $weight * $rentalDays, 2);
            $breakdown['rental_cost'] = $rentalCost;
            $breakdown['total'] = round($subtotal + $rentalCost, 2);
        }

        return $breakdown;
    }

    /**
     * Calculate fallback total price
     */
    private function calculateFallbackTotalPrice(string $carat, float $weight, ?int $rentalDays = null): float
    {
        $fallbackPrices = [
            '24' => 410,
            '22' => 370,
            '21' => 360,
            '20' => 340,
            '18' => 310,
            '16' => 270,
            '14' => 240,
            '10' => 200,
        ];

        $pricePerGram = $fallbackPrices[$carat] ?? 0;
        $totalPrice = $pricePerGram * $weight;

        $systemSetting = SystemSetting::first();
        // Add rental cost if applicable
       if ($rentalDays) {
            $dailyRate = $systemSetting->gold_rental_price_percentage??10;
            $dailyRate = $dailyRate/100;

            $rentalCostPerDay = ($totalPrice * $dailyRate) * $rentalDays??1;
            $totalPrice = $rentalCostPerDay;
        }else{
            $dailyRate = $systemSetting->gold_purchase_price??10;
            if($dailyRate < 0){
                $dailyRate = abs($dailyRate/100); // Make the rate negative for purchase discount
                $totalPrice = $totalPrice - $dailyRate; // Apply discount to total price
            }else{
                $dailyRate = abs($dailyRate/100); // Make the rate negative for purchase discount
                $totalPrice = $totalPrice + $dailyRate; // Apply discount to total price
            }
        }

        return round($totalPrice, 2);
    }

    /**
     * Get fallback price breakdown by type
     */
    private function getFallbackPriceBreakdownByType(string $carat, float $weight, string $type = 'buy', ?int $rentalDays = null): array
    {
        $fallbackStructuredPrices = $this->getFallbackStructuredPrices();
        
        if (!isset($fallbackStructuredPrices['prices'][$carat])) {
            throw new \InvalidArgumentException("Price for {$carat}k carat not found");
        }
        
        $caratData = $fallbackStructuredPrices['prices'][$carat];
        $pricePerGram = match($type) {
            'buy' => $caratData['buy_price'],
            'sell' => $caratData['sell_price'],
            'rental' => $caratData['rental_price_per_day'],
            default => throw new \InvalidArgumentException("Invalid transaction type: {$type}")
        };
        
        $breakdown = [
            'carat' => $carat,
            'weight' => $weight,
            'transaction_type' => $type,
            'original_price_per_gram' => $caratData['original_price'],
            'final_price_per_gram' => $pricePerGram,
            'adjustment' => $caratData["{$type}_adjustment"] ?? 0,
            'subtotal' => round($pricePerGram * $weight, 2),
            'rental_days' => $rentalDays,
            'total' => 0
        ];
        
        if ($type === 'rental' && $rentalDays) {
            $breakdown['total'] = round($pricePerGram * $weight * $rentalDays, 2);
        } else {
            $breakdown['total'] = $breakdown['subtotal'];
        }
        
        return $breakdown;
    }
}
