<?php

namespace App\Http\Requests\Vendor;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization will be handled by policy
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', Rule::in([Service::TYPE_CUPPING, Service::TYPE_MASSAGE])],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'available_sessions_per_day' => ['nullable', 'integer', 'min:1', 'max:999'],
            'duration' => ['nullable', 'integer', Rule::in(array_keys(Service::DURATIONS))],
            'max_concurrent_requests' => ['nullable', 'integer', 'min:1', 'max:99'],
            'location_type' => ['nullable', 'string', Rule::in([Service::LOCATION_HOME, Service::LOCATION_CENTER, Service::LOCATION_BOTH])],
            'branches' => ['nullable', 'array'],
            'branches.*' => ['exists:branches,id'],
            'images' => ['nullable', 'array', 'min:1', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB max
        ];
    }

    public function messages(): array
    {
        return [
            'name.nullable' => __('services.validation.name_nullable'),
            'price.nullable' => __('services.validation.price_nullable'),
            'price.numeric' => __('services.validation.price_numeric'),
            'images.nullable' => __('services.validation.image_nullable'),
            'images.*.mimes' => __('services.validation.image_format'),
            'images.*.max' => __('services.validation.image_size'),
            'duration.nullable' => __('services.validation.duration_nullable'),
            'duration.in' => __('services.validation.duration_invalid'),
            'type.nullable' => __('services.validation.type_nullable'),
            'type.in' => __('services.validation.type_invalid'),
            'location_type.nullable' => __('services.validation.location_type_nullable'),
            'location_type.in' => __('services.validation.location_type_invalid'),
            'available_sessions_per_day.nullable' => __('services.validation.sessions_nullable'),
            'available_sessions_per_day.min' => __('services.validation.sessions_min'),
            'max_concurrent_requests.nullable' => __('services.validation.max_requests_nullable'),
            'max_concurrent_requests.min' => __('services.validation.max_requests_min'),
        ];
    }
} 