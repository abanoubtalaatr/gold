<?php

namespace App\Http\Requests\Api\V1\LiquidationRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreLiquidationRequest extends FormRequest
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
            'amount' => ['nullable', 'numeric', 'min:0'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'bank_account_swift' => ['nullable', 'string', 'max:255'],
            'bank_account_iban' => ['required', 'string', 'max:255'],
            'bank_account_holder_name' => ['nullable', 'string', 'max:255'],
        ];
    }
} 