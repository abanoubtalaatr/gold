<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GoldPriceService
{
    /**
     * Get real-time gold prices from API
     */
    public function getRealTimePrices(): array
    {
        try {
            // Try to get from cache first
            $cacheKey = config('gold.cache.key');
            $cacheDuration = config('gold.cache.duration');
            
            $cachedPrices = Cache::get($cacheKey);
            if ($cachedPrices) {
                return $cachedPrices;
            }

            $response = Http::withHeaders([
                'x-access-token' => config('gold.api.token')
            ])->timeout(config('gold.api.timeout'))->get(config('gold.api.url'));

            if ($response->failed()) {
                Log::error('Failed to fetch gold prices', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new \Exception('Failed to fetch gold prices from API');
            }

            $data = $response->json();
            
            // Cache the response
            Cache::put($cacheKey, $data, $cacheDuration);
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error fetching gold prices: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get structured gold prices with buy, sell, and rental options
     */
    public function getStructuredGoldPrices(): array
    {
        $realTimePrices = $this->getRealTimePrices();
        $systemSetting = SystemSetting::first();
        
        // Calculate buy and sell rates as percentages
        $buyRate = ($systemSetting->gold_rental_price_percentage ?? 10) / 100;
        $sellRate = ($systemSetting->gold_purchase_price ?? 10) / 100;
        
        $carats = ['24', '22', '21', '20', '18', '16', '14', '10'];
        $structuredPrices = [];
        
        foreach ($carats as $carat) {
            $priceKey = "price_gram_{$carat}k";
            
            if (!isset($realTimePrices[$priceKey])) {
                continue;
            }
            
            $originalPrice = round($realTimePrices[$priceKey], 2);
            
            // Calculate prices using rates instead of fixed adjustments
            $buyPrice = round($originalPrice * (1 + $buyRate), 2);
            $sellPrice = round($originalPrice * (1 - $sellRate), 2);
            
            // Calculate rental price (daily rate)
            $dailyRentalRate = ($systemSetting->gold_rental_price_percentage ?? 10) / 100;
            $rentalPricePerDay = round($originalPrice * $dailyRentalRate, 2);
            
            $structuredPrices[$carat] = [
                'carat' => $carat,
                'original_price' => $originalPrice,
                'buy_price' => $buyPrice,
                'sell_price' => $sellPrice,
                'rental_price_per_day' => $rentalPricePerDay,
                'buy_rate' => $buyRate * 100, // Store as percentage
                'sell_rate' => $sellRate * 100, // Store as percentage
                'price_change_indicator' => $this->getPriceChangeIndicator($carat, $originalPrice),
            ];
        }
        
        return [
            'timestamp' => $realTimePrices['timestamp'] ?? now()->toISOString(),
            'currency' => $realTimePrices['currency'] ?? 'EGP',
            'prices' => $structuredPrices,
            'market_info' => [
                'metal' => $realTimePrices['metal'] ?? 'XAU',
                'exchange' => $realTimePrices['exchange'] ?? 'LBMA',
                'symbol' => $realTimePrices['symbol'] ?? 'XAU/EGP',
            ]
        ];
    }

    /**
     * Get prices formatted for mobile app display
     */
    public function getMobileFormattedPrices(): array
    {
        $structuredPrices = $this->getStructuredGoldPrices();
        
        $mobileFormat = [
            'banner_info' => [
                'main_carat' => '24',
                'buy_price' => $structuredPrices['prices']['24']['buy_price'] ?? 0,
                'sell_price' => $structuredPrices['prices']['24']['sell_price'] ?? 0,
                'date' => now()->format('j F'),
                'currency' => $structuredPrices['currency'],
            ],
            'price_list' => [],
            'timestamp' => $structuredPrices['timestamp'],
        ];
        
        foreach ($structuredPrices['prices'] as $caratData) {
            $carat = $caratData['carat'];
            
            $mobileFormat['price_list'][] = [
                'carat_key' => $carat,
                'carat' => "عيار {$carat}",
                'buy_price' => $caratData['buy_price'],
                'sell_price' => $caratData['sell_price'],
                'change_indicator' => $caratData['price_change_indicator'],
                'is_featured' => $carat === '24',
            ];
        }
        
        return $mobileFormat;
    }

    /**
     * Get price breakdown for specific transaction type
     */
    public function getPriceBreakdownByType(string $carat, float $weight, string $type = 'buy', ?int $rentalDays = null): array
    {
        $structuredPrices = $this->getStructuredGoldPrices();
        
        if (!isset($structuredPrices['prices'][$carat])) {
            throw new \InvalidArgumentException("Price for {$carat}k carat not found");
        }
        
        $caratData = $structuredPrices['prices'][$carat];
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

    /**
     * Get price change indicator (up/down/same)
     */
    private function getPriceChangeIndicator(string $carat, float $currentPrice): string
    {
        $cacheKey = "gold_price_history_{$carat}";
        $previousPrice = Cache::get($cacheKey);
        
        // Store current price for next comparison
        Cache::put($cacheKey, $currentPrice, now()->addHour());
        
        if ($previousPrice === null) {
            return 'same';
        }
        
        if ($currentPrice > $previousPrice) {
            return 'up';
        } elseif ($currentPrice < $previousPrice) {
            return 'down';
        }
        
        return 'same';
    }

    /**
     * Get formatted real-time gold prices with proper rounding
     */
    public function getFormattedRealTimePrices(): array
    {
        $realTimePrices = $this->getRealTimePrices();
        return $this->formatPriceData($realTimePrices);
    }

    /**
     * Get formatted real-time prices with adjustments applied
     */
    public function getFormattedRealTimePricesWithAdjustments(): array
    {
        $realTimePrices = $this->getRealTimePrices();
        $adjustments = $this->getPriceAdjustments();
        
        // First format all the base prices
        $formattedData = $this->formatPriceData($realTimePrices);
        
        // Apply adjustments and round again
        foreach ($adjustments as $carat => $adjustment) {
            $priceKey = "price_gram_{$carat}k";
            if (isset($formattedData[$priceKey])) {
                $formattedData[$priceKey] = round($formattedData[$priceKey] + $adjustment, 2);
            }
        }
        
        return $formattedData;
    }

    /**
     * Get adjusted price for specific carat
     */
    public function getAdjustedPricePerGram(string $carat): float
    {
        $realTimePrices = $this->getRealTimePrices();
        $priceKey = "price_gram_{$carat}k";
        
        if (!isset($realTimePrices[$priceKey])) {
            throw new \InvalidArgumentException("Price for {$carat}k carat not found");
        }

        $basePrice = $realTimePrices[$priceKey];
        // for sell price
        $adjustment = config("gold.price_adjustments.{$carat}", 0);
        
        return round($basePrice + $adjustment, 2);
    }

    /**
     * Get all adjusted prices per gram
     */
    public function getAllAdjustedPrices(): array
    {
        $realTimePrices = $this->getRealTimePrices();
        $adjustedPrices = [];
        $adjustments = config('gold.price_adjustments', []);

        foreach ($adjustments as $carat => $adjustment) {
            $priceKey = "price_gram_{$carat}k";
            if (isset($realTimePrices[$priceKey])) {
                $adjustedPrices[$carat] = $realTimePrices[$priceKey] + $adjustment;
            }
        }

        return $adjustedPrices;
    }

    /**
     * Calculate total price for given weight and carat
     */
    public function calculateTotalPrice(string $carat, float $weight, ?int $rentalDays = null): float
    {
        $pricePerGram = $this->getAdjustedPricePerGram($carat);
        $totalPrice = $pricePerGram * $weight;
        $systemSetting = SystemSetting::first();
        // Add rental cost if applicable
        if ($rentalDays) {
            $dailyRate = $systemSetting->gold_rental_price_percentage??10;
            $dailyRate = $dailyRate/100;

            $rentalCostPerDay = $pricePerGram * $dailyRate;
            $totalPrice += $rentalCostPerDay * $weight * $rentalDays;
        }else{

            $dailyRate = $systemSetting->gold_purchase_price??10;
            $dailyRate = $dailyRate/100;

            $totalPrice += $pricePerGram * $weight;
        }

        return round($totalPrice, 2);
    }

    /**
     * Get price adjustments configuration
     */
    public function getPriceAdjustments(): array
    {
        return config('gold.price_adjustments', []);
    }

    /**
     * Get price breakdown for transparency
     */
    public function getPriceBreakdown(string $carat, float $weight, ?int $rentalDays = null): array
    {
        $realTimePrices = $this->getRealTimePrices();
        $priceKey = "price_gram_{$carat}k";
        
        if (!isset($realTimePrices[$priceKey])) {
            throw new \InvalidArgumentException("Price for {$carat}k carat not found");
        }

        $basePrice = $realTimePrices[$priceKey];
        $adjustment = config("gold.price_adjustments.{$carat}", 0);
        $adjustedPricePerGram = $basePrice + $adjustment;
        $subtotal = $adjustedPricePerGram * $weight;
        
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
            $dailyRate = config('gold.rental.daily_rate_percentage', 0.01);
            $rentalCostPerDay = $adjustedPricePerGram * $dailyRate;
            $rentalCost = $rentalCostPerDay * $weight * $rentalDays;
            $breakdown['rental_cost'] = $rentalCost;
            $breakdown['total'] = $subtotal + $rentalCost;
        }

        // Round all monetary values
        foreach (['base_price_per_gram', 'final_price_per_gram', 'subtotal', 'rental_cost', 'total'] as $field) {
            $breakdown[$field] = round($breakdown[$field], 2);
        }

        return $breakdown;
    }

    /**
     * Format price data by rounding all monetary values to 2 decimal places
     */
    private function formatPriceData(array $data): array
    {
        // Round main price fields
        $priceFields = ['ask', 'bid', 'price', 'ch'];
        foreach ($priceFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = round($data[$field], 2);
            }
        }
        
        // Round all gram prices
        $gramPriceFields = [
            'price_gram_24k', 'price_gram_22k', 'price_gram_21k', 'price_gram_20k',
            'price_gram_18k', 'price_gram_16k', 'price_gram_14k', 'price_gram_10k'
        ];
        foreach ($gramPriceFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = round($data[$field], 2);
            }
        }
        
        return $data;
    }

    /**
     * Get current market data with adjustments applied
     */
    public function getMarketDataWithAdjustments(): array
    {
        $realTimePrices = $this->getRealTimePrices();
        $adjustedPrices = $this->getAllAdjustedPrices();
        
        // Format original prices
        $formattedOriginalPrices = $this->formatPriceData($realTimePrices);
        
        // Round adjusted prices
        foreach ($adjustedPrices as $carat => $price) {
            $adjustedPrices[$carat] = round($price, 2);
        }
        
        return [
            'timestamp' => $realTimePrices['timestamp'],
            'metal' => $realTimePrices['metal'],
            'currency' => $realTimePrices['currency'],
            'exchange' => $realTimePrices['exchange'],
            'symbol' => $realTimePrices['symbol'],
            'original_prices' => [
                'price_gram_24k' => $formattedOriginalPrices['price_gram_24k'] ?? null,
                'price_gram_22k' => $formattedOriginalPrices['price_gram_22k'] ?? null,
                'price_gram_21k' => $formattedOriginalPrices['price_gram_21k'] ?? null,
                'price_gram_20k' => $formattedOriginalPrices['price_gram_20k'] ?? null,
                'price_gram_18k' => $formattedOriginalPrices['price_gram_18k'] ?? null,
                'price_gram_16k' => $formattedOriginalPrices['price_gram_16k'] ?? null,
                'price_gram_14k' => $formattedOriginalPrices['price_gram_14k'] ?? null,
                'price_gram_10k' => $formattedOriginalPrices['price_gram_10k'] ?? null,
            ],
            'adjusted_prices' => $adjustedPrices,
            'adjustments' => $this->getPriceAdjustments()
        ];
    }
} 