<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gold API Configuration
    |--------------------------------------------------------------------------
    */
    'api' => [
        'url' => env('GOLD_API_URL', 'https://www.goldapi.io/api/XAU/SAR'),
        'token' => env('GOLD_API_TOKEN', 'goldapi-1giwusmbeuudi4-io'),
        'timeout' => env('GOLD_API_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'key' => 'gold_prices',
        'duration' => env('GOLD_CACHE_DURATION', 300), // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Price Adjustments per Carat
    |--------------------------------------------------------------------------
    | 
    | These values will be added to (or subtracted from) the real-time prices
    | from the API. You can use positive values to add margins/fees or
    | negative values to provide discounts.
    |
    | Values are in SAR per gram.
    | 
    | - buy: adjustments for buying from customers (we pay customers)
    | - sell: adjustments for selling to customers (customers pay us)
    */
    'adjustments' => [
        'buy' => [
            '24' => env('GOLD_BUY_ADJUSTMENT_24K', -10),   // Buy 10 SAR below market
            '22' => env('GOLD_BUY_ADJUSTMENT_22K', -15),   // Buy 15 SAR below market
            '21' => env('GOLD_BUY_ADJUSTMENT_21K', -20),   // Buy 20 SAR below market
            '20' => env('GOLD_BUY_ADJUSTMENT_20K', -25),   // Buy 25 SAR below market
            '18' => env('GOLD_BUY_ADJUSTMENT_18K', -30),   // Buy 30 SAR below market
            '16' => env('GOLD_BUY_ADJUSTMENT_16K', -35),   // Buy 35 SAR below market
            '14' => env('GOLD_BUY_ADJUSTMENT_14K', -40),   // Buy 40 SAR below market
            '10' => env('GOLD_BUY_ADJUSTMENT_10K', -45),   // Buy 45 SAR below market
        ],
        'sell' => [
            '24' => env('GOLD_SELL_ADJUSTMENT_24K', 15),   // Sell 15 SAR above market
            '22' => env('GOLD_SELL_ADJUSTMENT_22K', 20),   // Sell 20 SAR above market
            '21' => env('GOLD_SELL_ADJUSTMENT_21K', 25),   // Sell 25 SAR above market
            '20' => env('GOLD_SELL_ADJUSTMENT_20K', 30),   // Sell 30 SAR above market
            '18' => env('GOLD_SELL_ADJUSTMENT_18K', 35),   // Sell 35 SAR above market
            '16' => env('GOLD_SELL_ADJUSTMENT_16K', 40),   // Sell 40 SAR above market
            '14' => env('GOLD_SELL_ADJUSTMENT_14K', 45),   // Sell 45 SAR above market
            '10' => env('GOLD_SELL_ADJUSTMENT_10K', 50),   // Sell 50 SAR above market
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Price Adjustments (Backward Compatibility)
    |--------------------------------------------------------------------------
    */
    'price_adjustments' => [
        '24' => env('GOLD_ADJUSTMENT_24K', 5),    // Pure gold - no adjustment
        '22' => env('GOLD_ADJUSTMENT_22K', 5),    // Add 5 SAR per gram
        '21' => env('GOLD_ADJUSTMENT_21K', 10),   // Add 10 SAR per gram
        '20' => env('GOLD_ADJUSTMENT_20K', 15),   // Add 15 SAR per gram
        '18' => env('GOLD_ADJUSTMENT_18K', 20),   // Add 20 SAR per gram
        '16' => env('GOLD_ADJUSTMENT_16K', 25),   // Add 25 SAR per gram
        '14' => env('GOLD_ADJUSTMENT_14K', 30),   // Add 30 SAR per gram
        '10' => env('GOLD_ADJUSTMENT_10K', 35),   // Add 35 SAR per gram
    ],

    /*
    |--------------------------------------------------------------------------
    | Rental Configuration
    |--------------------------------------------------------------------------
    */
    'rental' => [
        'daily_rate_percentage' => env('GOLD_RENTAL_DAILY_RATE', 0.01), // 1% per day
        'weekly_multiplier' => 7,
        'monthly_multiplier' => 30,
        'min_rental_days' => env('GOLD_MIN_RENTAL_DAYS', 1),
        'max_rental_days' => env('GOLD_MAX_RENTAL_DAYS', 365),
        'deposit_percentage' => env('GOLD_RENTAL_DEPOSIT', 0.2), // 20% deposit
    ],
]; 