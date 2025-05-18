<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoldPieceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'weight' => 'sometimes|numeric|min:0.1',
            'carat' => 'sometimes|in:18,21,24',
            'type' => 'sometimes|in:for_rent,for_sale',
            'description' => 'nullable|string',
            'rental_price_per_day' => 'required_if:type,for_rent|numeric|min:0',
            'sale_price' => 'required_if:type,for_sale|numeric|min:0',
            'images' => 'sometimes|array|min:1|max:5',
            'images.*' => 'required|image|mimes:jpeg,png|max:5120', // 5MB max
            'remove_image_ids' => 'sometimes|array',
            'remove_image_ids.*' => 'exists:media,id'
        ];
    }

    public function messages()
    {
        return [
            'images.array' => 'Images must be provided as an array',
            'images.min' => 'Please upload at least one image',
            'images.max' => 'You can upload up to 5 images',
            'images.*.required' => 'Each image is required',
            'images.*.image' => 'Files must be images',
            'images.*.mimes' => 'Images must be in JPEG or PNG format',
            'images.*.max' => 'Each image must not exceed 5MB',
            'rental_price_per_day.required_if' => 'Rental price is required for rental items',
            'sale_price.required_if' => 'Sale price is required for items for sale',
            'remove_image_ids.array' => 'Remove image IDs must be provided as an array',
            'remove_image_ids.*.exists' => 'One or more image IDs are invalid'
        ];
    }
} 