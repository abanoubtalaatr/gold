<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'message' => 'required|string',
            'subject' => 'required|string',
            'sale_order_id' => 'required_without:rental_order_id|integer|exists:order_sales,id',
            'rental_order_id' => 'required_without:sale_order_id|integer|exists:order_rentals,id',
        ];
    }
}