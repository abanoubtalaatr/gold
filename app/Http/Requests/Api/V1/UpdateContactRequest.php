<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
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
            'message' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string|max:255',
            'sale_order_id' => 'sometimes|nullable|integer',
            'rental_order_id' => 'sometimes|nullable|integer',
        ];
    }
    public function messages()
    {
        return [
            // 'name.required' => 'The name field is required.',
            // 'email.email' => 'Please provide a valid email address.',
        ];
    }
}
