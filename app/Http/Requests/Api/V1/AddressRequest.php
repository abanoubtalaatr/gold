<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'street_name' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'address' => 'required|string',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'is_default' => 'sometimes|boolean',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
        ];

        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            $rules = collect($rules)->map(function ($value) {
                return str_replace('required|', 'sometimes|', $value);
            })->all();
        }

        return $rules;
    }
} 