<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'mobile' => 'required|string|max:20|unique:users',
            'store_name_en' => 'required|string|max:255',
            'store_name_ar' => 'required|string|max:255',
            'commercial_registration_number' => 'required|string|max:255|unique:users',
            'commercial_registration_image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}