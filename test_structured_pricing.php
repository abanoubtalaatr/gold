<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Services\GoldPriceService;

echo "=== Gold Pricing System - Structured Response Demo ===\n\n";

$goldPriceService = app(GoldPriceService::class);

try {
    // 1. Get structured pricing data
    echo "1. Structured Gold Prices (Original API + Buy/Sell/Rental Adjustments):\n";
    echo str_repeat("=", 70) . "\n";
    
    $structuredPrices = $goldPriceService->getStructuredGoldPrices();
    
    echo "Timestamp: " . $structuredPrices['timestamp'] . "\n";
    echo "Currency: " . $structuredPrices['currency'] . "\n\n";
    
    foreach ($structuredPrices['prices'] as $carat => $data) {
        echo "Carat {$carat}k:\n";
        echo "  Original API Price: {$data['original_price']} SAR/gram\n";
        echo "  Buy Price (We Pay):  {$data['buy_price']} SAR/gram (adjustment: {$data['buy_adjustment']})\n";
        echo "  Sell Price (Customer Pays): {$data['sell_price']} SAR/gram (adjustment: {$data['sell_adjustment']})\n";
        echo "  Rental Price: {$data['rental_price_per_day']} SAR/gram/day\n";
        echo "  Price Trend: {$data['price_change_indicator']}\n\n";
    }

    // 2. Get mobile formatted data
    echo "\n2. Mobile App Formatted Response:\n";
    echo str_repeat("=", 70) . "\n";
    
    $mobileFormat = $goldPriceService->getMobileFormattedPrices();
    
    echo "Banner Info (24k Gold):\n";
    echo "  Buy Price: {$mobileFormat['banner_info']['buy_price']} {$mobileFormat['banner_info']['currency']}\n";
    echo "  Sell Price: {$mobileFormat['banner_info']['sell_price']} {$mobileFormat['banner_info']['currency']}\n";
    echo "  Date: {$mobileFormat['banner_info']['date']}\n\n";
    
    echo "Prices List (Combined Buy & Sell):\n";
    foreach ($mobileFormat['price_list'] as $item) {
        $featured = $item['is_featured'] ? ' (Featured)' : '';
        echo "  {$item['carat']} (Key: {$item['carat_key']}): Buy {$item['buy_price']} SAR | Sell {$item['sell_price']} SAR - {$item['change_indicator']}{$featured}\n";
    }

    // 3. Example price calculations
    echo "\n\n3. Price Calculation Examples:\n";
    echo str_repeat("=", 70) . "\n";
    
    $examples = [
        ['type' => 'buy', 'carat' => '22', 'weight' => 10],
        ['type' => 'sell', 'carat' => '22', 'weight' => 10],
        ['type' => 'rental', 'carat' => '22', 'weight' => 10, 'days' => 30],
    ];
    
    foreach ($examples as $example) {
        $breakdown = $goldPriceService->getPriceBreakdownByType(
            $example['carat'], 
            $example['weight'], 
            $example['type'],
            $example['days'] ?? null
        );
        
        echo "Example: {$breakdown['transaction_type']} {$breakdown['weight']}g of {$breakdown['carat']}k gold";
        if (isset($example['days'])) {
            echo " for {$example['days']} days";
        }
        echo "\n";
        
        echo "  Original Price: {$breakdown['original_price_per_gram']} SAR/gram\n";
        echo "  Final Price: {$breakdown['final_price_per_gram']} SAR/gram\n";
        echo "  Adjustment: {$breakdown['adjustment']} SAR/gram\n";
        echo "  Subtotal: {$breakdown['subtotal']} SAR\n";
        echo "  Total: {$breakdown['total']} SAR\n\n";
    }

    // 4. JSON format example
    echo "\n4. API Response Example (JSON format):\n";
    echo str_repeat("=", 70) . "\n";
    echo json_encode($mobileFormat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "This is likely because the gold API is not configured or accessible.\n";
    echo "Please check your .env file for GOLD_API_URL and GOLD_API_TOKEN\n";
}

echo "\n=== Test Complete ===\n"; 