<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'city_id' => ['required', 'integer', 'exists:cities,id'],
        'working_days' => ['required', 'array'],
        'working_days.*' => ['required', 'integer', 'between:0,6'],
        'working_hours' => ['required', 'array'],

        'images' => ['nullable', 'array', 'max:5'],
        'images.*' => ['image', 'mimes:jpg,png', 'max:5120'],
    ];
}

    public function messages(): array
    {
        return [
            'name.required' => __('Please enter the branch name.'),
            'city_id.required' => __('Please select a city.'),
            'city_id.exists' => __('Selected city is invalid.'),
            'working_days.required' => __('Please select working days.'),
            'working_days.min' => __('Please select at least one working day.'),
            'working_hours.required' => __('Please enter working hours.'),
            'services.required' => __('Please select at least one service.'),
            'services.min' => __('Please select at least one service.'),
            'images.max' => __('Maximum 5 images allowed.'),
            'images.*.max' => __('Each image must not exceed 5MB.'),
        ];
    }
}
