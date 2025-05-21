<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoldPieceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
            'carat' => 'required|in:18,21,24',
            'type' => 'required|in:for_rent,for_sale',
            'description' => 'nullable|string',
            'rental_price_per_day' => 'required_if:type,for_rent|nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            "branch_id" => "nullable|exists:branches,id",
            "deposit_amount" => "required_if:type,for_rent|nullable|numeric|min:0",
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|image|mimes:jpeg,png|max:5120', // 5MB max
        ];
    }
}