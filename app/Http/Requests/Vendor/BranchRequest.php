<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'working_days' => ['required', 'array'],
            'working_days.*' => ['required', 'integer', 'between:0,6'],
            'working_hours' => ['required', 'array'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'removeLogo' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,png', 'max:5120'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'number_of_available_items' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
