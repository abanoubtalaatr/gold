<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SlidersRequest extends FormRequest
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
             'image' => 'required|image|max:5120', // 5MB max
            'display_from' => 'required|date',
            'display_to' => 'required|date|after:display_from',
            'display_order' => 'required|integer|min:1',
        
        ];
    }
}
