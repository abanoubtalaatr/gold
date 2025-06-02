<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GoldPicesUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0.01',
            'carat' => ['required', Rule::in(['18', '21', '22', '24'])],
            'type' => ['required', Rule::in(['for_rent', 'for_sale'])],
            'status' => [
                'required',
                Rule::in(['pending', 'accepted', 'rented', 'sold', 'available']),
                function ($attribute, $value, $fail) {
                    if ($this->type === 'for_sale' && !in_array($value, ['pending', 'accepted', 'sold'])) {
                        $fail('The selected status is invalid for gold pieces for sale.');
                    }
                    if ($this->type === 'for_rent' && !in_array($value, ['pending', 'accepted', 'rented', 'available', 'sold'])) {
                        $fail('The selected status is invalid for gold pieces for rent.');
                    }
                },
            ],
            'rental_price_per_day' => [
                Rule::requiredIf($this->type === 'for_rent'),
                'nullable',
                'numeric',
                'min:0'
            ],
            'sale_price' => [
                Rule::requiredIf($this->type === 'for_sale'),
                'nullable',
                'numeric',
                'min:0'
            ],
            'deposit_amount' => 'nullable|numeric|min:0',
            'is_including_lobes' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'deleted_images' => 'nullable|array',
            // 'deleted_images.*' => 'exists:images,id',
            'delete_commercial_registration_image' => 'nullable|boolean',
            'delete_logo' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Invalid status. Valid values are: pending, accepted, rented, sold, available.',
            'rental_price_per_day.required_if' => 'The rental price per day is required when type is for rent.',
            'sale_price.required_if' => 'The sale price is required when type is for sale.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'description' => $this->description ?: null,
            'rental_price_per_day' => $this->rental_price_per_day ?: null,
            'sale_price' => $this->sale_price ?: null,
            'deposit_amount' => $this->deposit_amount ?: null,
            'is_including_lobes' => (bool) $this->is_including_lobes,
            'deleted_images' => $this->deleted_images ?: [],
        ]);
    }
}
