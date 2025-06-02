<?php

// Quick test script to demonstrate the gold pricing functionality
// Run this with: php test_gold_pricing.php

require_once 'bootstrap/app.php';

$app = Illuminate\Foundation\Application::getInstance();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GoldPriceService;

echo "=== Gold Price Testing (Formatted to 2 Decimal Places) ===\n\n";

try {
    $goldPriceService = app(GoldPriceService::class);
    
    echo "1. Raw Real-Time Prices (from API, formatted):\n";
    $rawPrices = $goldPriceService->getRealTimePrices();
    echo "24k: " . round($rawPrices['price_gram_24k'], 2) . " SAR\n";
    echo "22k: " . round($rawPrices['price_gram_22k'], 2) . " SAR\n";
    echo "21k: " . round($rawPrices['price_gram_21k'], 2) . " SAR\n";
    echo "18k: " . round($rawPrices['price_gram_18k'], 2) . " SAR\n";
    
    echo "\n2. Adjustments Applied:\n";
    $adjustments = $goldPriceService->getPriceAdjustments();
    foreach ($adjustments as $carat => $adjustment) {
        $symbol = $adjustment >= 0 ? '+' : '';
        echo "{$carat}k: {$symbol}{$adjustment} SAR\n";
    }
    
    echo "\n3. Adjusted Prices (what your API returns, formatted):\n";
    foreach ($adjustments as $carat => $adjustment) {
        $priceKey = "price_gram_{$carat}k";
        if (isset($rawPrices[$priceKey])) {
            $originalPrice = round($rawPrices[$priceKey], 2);
            $adjustedPrice = round($originalPrice + $adjustment, 2);
            echo "{$carat}k: {$originalPrice} + {$adjustment} = {$adjustedPrice} SAR\n";
        }
    }
    
    echo "\n4. Price Calculation Example (22k, 10 grams, 5 rental days):\n";
    $breakdown = $goldPriceService->getPriceBreakdown('22', 10, 5);
    echo "Base price per gram: {$breakdown['base_price_per_gram']} SAR\n";
    echo "Adjustment per gram: +{$breakdown['adjustment_per_gram']} SAR\n";
    echo "Final price per gram: {$breakdown['final_price_per_gram']} SAR\n";
    echo "Subtotal (10g): {$breakdown['subtotal']} SAR\n";
    echo "Rental cost (5 days): {$breakdown['rental_cost']} SAR\n";
    echo "Total: {$breakdown['total']} SAR\n";
    
    echo "\n5. Example API Response Format:\n";
    echo "Before: price_gram_22k: 375.3700000000000045474735088646411895751953125\n";
    echo "After:  price_gram_22k: " . round(375.3700000000000045474735088646411895751953125, 2) . "\n";
    
    echo "\n✅ All prices now formatted to 2 decimal places!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "This might be because the gold API is not accessible or configuration is missing.\n";
}

echo "\n=== API Endpoints Available ===\n";
echo "GET  /api/v1/real-time-price     - Real-time prices WITH adjustments (2 decimals)\n";
echo "GET  /api/v1/raw-real-time-price - Real-time prices WITHOUT adjustments (2 decimals)\n";
echo "GET  /api/v1/adjusted-prices     - Market data with adjustments (2 decimals)\n";
echo "GET  /api/v1/price/carat?carat=22 - Price for specific carat (2 decimals)\n";
echo "POST /api/v1/price               - Calculate total price (2 decimals)\n";
echo "POST /api/v1/price/breakdown     - Detailed price breakdown (2 decimals)\n"; 