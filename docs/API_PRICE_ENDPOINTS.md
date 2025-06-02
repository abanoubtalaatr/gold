# Gold Price API Endpoints

This document describes the enhanced gold price API endpoints with real-time pricing and adjustable margins.

## Configuration

Gold prices can be adjusted per carat by modifying the `config/gold.php` file or setting environment variables:

```env
GOLD_ADJUSTMENT_24K=0     # No adjustment for pure gold
GOLD_ADJUSTMENT_22K=5     # Add 5 SAR per gram
GOLD_ADJUSTMENT_21K=10    # Add 10 SAR per gram
GOLD_ADJUSTMENT_18K=20    # Add 20 SAR per gram
# ... etc
```

## Endpoints

### 1. Calculate Total Price
**POST** `/api/v1/price`

Calculates the total price including rental costs if applicable.

**Request Body:**
```json
{
    "carat": "22",
    "weight": 10.5,
    "number_rental_day": 30
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_price": 4200.75,
        "carat": "22",
        "weight": 10.5,
        "rental_days": 30
    }
}
```

### 2. Get Price Breakdown
**POST** `/api/v1/price/breakdown`

Returns detailed price breakdown showing base price, adjustments, and rental costs.

**Request Body:**
```json
{
    "carat": "22",
    "weight": 10.5,
    "number_rental_day": 30
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "carat": "22",
        "weight": 10.5,
        "base_price_per_gram": 370.57,
        "adjustment_per_gram": 5,
        "final_price_per_gram": 375.57,
        "subtotal": 3943.49,
        "rental_days": 30,
        "rental_cost": 1127.21,
        "total": 5070.70
    }
}
```

### 3. Get Real-Time Prices (with adjustments)
**GET** `/api/v1/real-time-price`

Returns real-time gold prices with your configured adjustments applied to each carat.

**Response:**
```json
{
    "success": true,
    "data": {
        "timestamp": 1748855120,
        "metal": "XAU",
        "currency": "SAR",
        "price_gram_24k": 404.26,
        "price_gram_22k": 375.57,
        "price_gram_21k": 363.73,
        "price_gram_18k": 323.19,
        // ... other carats (all with adjustments applied)
    }
}
```

### 3b. Get Raw Real-Time Prices (without adjustments)
**GET** `/api/v1/raw-real-time-price`

Returns raw real-time gold prices from the external API without any adjustments.

**Response:**
```json
{
    "success": true,
    "data": {
        "timestamp": 1748855120,
        "metal": "XAU",
        "currency": "SAR",
        "price_gram_24k": 404.259,
        "price_gram_22k": 370.5707,
        "price_gram_21k": 353.7266,
        "price_gram_18k": 303.1942,
        // ... other carats (original API prices)
    }
}
```

### 4. Get Adjusted Prices
**GET** `/api/v1/adjusted-prices`

Returns current market data with adjustments applied.

**Response:**
```json
{
    "success": true,
    "data": {
        "timestamp": 1748855120,
        "metal": "XAU",
        "currency": "SAR",
        "original_prices": {
            "price_gram_24k": 404.259,
            "price_gram_22k": 370.5707,
            // ... other carats
        },
        "adjusted_prices": {
            "24": 404.259,
            "22": 375.5707,
            "21": 363.7266,
            "18": 323.1942
            // ... other carats
        },
        "adjustments": {
            "24": 0,
            "22": 5,
            "21": 10,
            "18": 20
            // ... other carats
        }
    }
}
```

### 5. Get Price for Specific Carat
**GET** `/api/v1/price/carat?carat=22`

Returns the adjusted price per gram for a specific carat.

**Query Parameters:**
- `carat`: Required. One of: 10, 14, 16, 18, 20, 21, 22, 24

**Response:**
```json
{
    "success": true,
    "data": {
        "carat": "22",
        "price_per_gram": 375.57,
        "currency": "SAR"
    }
}
```

## Error Responses

All endpoints return error responses in the following format:

```json
{
    "success": false,
    "message": "Error message",
    "data": null
}
```

Common error scenarios:
- Invalid carat value
- API connection failed
- Price not found for specified carat
- Validation errors

## Caching

Real-time prices are cached for 5 minutes (configurable via `GOLD_CACHE_DURATION`) to improve performance and reduce API calls.

## Price Adjustments

Price adjustments are applied to the base real-time prices and can be:
- **Positive**: Add margins, fees, or markups
- **Negative**: Apply discounts or reductions
- **Zero**: No adjustment (use raw API price)

Adjustments are configurable per carat in the `config/gold.php` file. 