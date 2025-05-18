<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization will be handled by policy
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['required', 'string', 'max:1000'],
            'working_days' => ['required', 'array'],
            'working_days.*' => ['required', 'string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'working_hours' => ['required', 'array'],
            'working_hours.*.open' => ['required', 'date_format:H:i'],
            'working_hours.*.close' => ['required', 'date_format:H:i', 'after:working_hours.*.open'],
            'services' => ['nullable', 'array'],
            'services.*' => ['string', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('branches.name')]),
            'latitude.required' => __('validation.required', ['attribute' => __('branches.latitude')]),
            'latitude.between' => __('validation.between.numeric', ['attribute' => __('branches.latitude'), 'min' => -90, 'max' => 90]),
            'longitude.required' => __('validation.required', ['attribute' => __('branches.longitude')]),
            'longitude.between' => __('validation.between.numeric', ['attribute' => __('branches.longitude'), 'min' => -180, 'max' => 180]),
            'address.required' => __('validation.required', ['attribute' => __('branches.address')]),
            'working_days.required' => __('validation.required', ['attribute' => __('branches.working_days')]),
            'working_days.*.in' => __('validation.in', ['attribute' => __('branches.day')]),
            'working_hours.required' => __('validation.required', ['attribute' => __('branches.working_hours')]),
            'working_hours.*.open.required' => __('validation.required', ['attribute' => __('branches.opening_time')]),
            'working_hours.*.close.required' => __('validation.required', ['attribute' => __('branches.closing_time')]),
            'working_hours.*.close.after' => __('validation.after', ['attribute' => __('branches.closing_time'), 'date' => __('branches.opening_time')]),
            'images.*.image' => __('validation.image', ['attribute' => __('branches.image')]),
            'images.*.max' => __('validation.max.file', ['attribute' => __('branches.image'), 'max' => 2]),
        ];
    }
} 