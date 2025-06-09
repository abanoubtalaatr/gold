<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'platform_commission_percentage',
        'merchant_commission_percentage',
        'tax_percentage',
        'gold_purchase_price',
        'gold_purchase_additional_percentage',
        'gold_rental_price_percentage',
        'gold_rental_insurance_percentage',
        'booking_insurance_amount',
        'minimum_payout_amount',
        'max_delivery_time_hours',
        'privacy_policy',
        'terms_conditions',
        'contact_phone',
        'contact_email',
        'location_map'
    ];

    protected $casts = [
        'platform_commission_percentage' => 'decimal:2',
        'merchant_commission_percentage' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'gold_purchase_price' => 'decimal:2',
        'gold_purchase_additional_percentage' => 'decimal:2',
        'gold_rental_price_percentage' => 'decimal:2',
        'gold_rental_insurance_percentage' => 'decimal:2',
        'booking_insurance_amount' => 'decimal:2',
    ];

    /**
     * Get a specific system setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function value(string $key, $default = null)
    {
        $setting = static::first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->getAttribute($key) ?? $default;
    }
}
