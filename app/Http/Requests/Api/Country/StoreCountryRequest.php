<?php

namespace App\Http\Requests\Api\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCountryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:countries,name',
            'name_ar' => 'required|string|max:255|unique:countries,name_ar',
            'is_active' => 'boolean',
            'iso3' => 'required|string|size:3|unique:countries,iso3',
            'iso2' => 'required|string|size:2|unique:countries,iso2',
            'phonecode' => 'required|string|max:10',
            'capital' => 'required|string|max:255',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'region' => 'required|string|max:255',
            'subregion' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The country name is required.',
            'name.unique' => 'This country name already exists.',
            'name_ar.required' => 'The Arabic country name is required.',
            'name_ar.unique' => 'This Arabic country name already exists.',
            'iso3.required' => 'The ISO3 code is required.',
            'iso3.size' => 'The ISO3 code must be exactly 3 characters.',
            'iso3.unique' => 'This ISO3 code already exists.',
            'iso2.required' => 'The ISO2 code is required.',
            'iso2.size' => 'The ISO2 code must be exactly 2 characters.',
            'iso2.unique' => 'This ISO2 code already exists.',
            'phonecode.required' => 'The phone code is required.',
            'capital.required' => 'The capital city is required.',
            'currency.required' => 'The currency code is required.',
            'currency_symbol.required' => 'The currency symbol is required.',
            'region.required' => 'The region is required.',
            'subregion.required' => 'The subregion is required.',
            'latitude.required' => 'The latitude is required.',
            'latitude.between' => 'The latitude must be between -90 and 90.',
            'longitude.required' => 'The longitude is required.',
            'longitude.between' => 'The longitude must be between -180 and 180.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
} 