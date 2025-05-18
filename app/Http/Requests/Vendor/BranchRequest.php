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
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['required', 'string', 'max:500'],
            'working_days' => ['required', 'array', 'min:1'],
            'working_days.*' => ['required', 'integer', 'between:0,6'],
            'working_hours' => ['required', 'array'],
            'working_hours.*.open' => ['required', 'date_format:H:i'],
            'working_hours.*.close' => ['required', 'date_format:H:i'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['required', 'integer', 'exists:services,id'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,png', 'max:5120'], // 5MB max
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Please enter the branch name.'),
            'latitude.required' => __('Please select the branch location.'),
            'longitude.required' => __('Please select the branch location.'),
            'address.required' => __('Please enter the branch address.'),
            'working_days.required' => __('Please select working days.'),
            'working_days.min' => __('Please select at least one working day.'),
            'working_hours.required' => __('Please enter working hours.'),
            'services.required' => __('Please select at least one service for this branch.'),
            'services.min' => __('Please select at least one service for this branch.'),
            'images.max' => __('Maximum 5 images allowed.'),
            'images.*.max' => __('Each image must not exceed 5MB.'),
        ];
    }
} 