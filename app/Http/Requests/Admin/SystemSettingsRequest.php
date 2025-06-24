<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SystemSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Commission settings
            'platform_commission_percentage' => 'required|numeric|min:0|max:100',
            'merchant_commission_percentage' => 'required|numeric|min:0|max:100',

            // Tax settings
            'tax_percentage' => 'required|numeric|min:0|max:100',

            // Gold settings
            'gold_purchase_price' => 'required|numeric|min:0',
            'gold_purchase_additional_percentage' => 'required|numeric',
            'gold_rental_price_percentage' => 'required|numeric|min:0',
            'gold_rental_insurance_percentage' => 'required|numeric|min:0',

            // Order settings
            'booking_insurance_amount' => 'required|numeric|min:0',
            'minimum_payout_amount' => 'required|numeric|min:0',
            'max_delivery_time_hours' => 'required|integer|min:1',

            // Website content
            'privacy_policy' => 'nullable|string',
            'terms_conditions' => 'nullable|string',

            // Contact info
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'location_map' => 'nullable|string',

            // Order settings
            'max_canceled_orders' => 'required|integer|min:0',
            'vendor_debt_limit' => 'required|numeric|min:0',
            'max_active_orders' => 'required|integer|min:0',
        ];
    }
}
