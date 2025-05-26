<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $vendor = $this->user();

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$vendor->id,
            'mobile' => 'required|string|max:20|unique:users,mobile,'.$vendor->id,
            'dialling_code' => 'required|string|max:10',
            'store_name_en' => 'required|string|max:255',
            'store_name_ar' => 'required|string|max:255',
            'commercial_number' => 'required|string|max:255|unique:users,commercial_registration_number,'.$vendor->id,
            'commercial_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'iban' => 'required|string|max:255',
            'address.address' => 'required|string|max:500',
            'address.city_id' => 'required|exists:cities,id',
            'working_hours' => 'required|array',
            'working_hours.*.day' => 'required|string|max:20',
            'working_hours.*.open' => 'required|date_format:H:i',
            'working_hours.*.close' => 'required|date_format:H:i',
            'working_hours.*.closed' => 'required|boolean'
        ];
    }
}
