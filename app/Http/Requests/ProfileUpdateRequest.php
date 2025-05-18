<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }


    public function messages(): array
{
    return [
        'name.required' => trans('validation.required', ['attribute' => trans('auth.name')]),
        'name.string' => trans('validation.string', ['attribute' => trans('auth.name')]),
        'name.max' => trans('validation.max.string', ['attribute' => trans('auth.name'), 'max' => 255]),

        'email.required' => trans('validation.required', ['attribute' => trans('auth.email')]),
        'email.string' => trans('validation.string', ['attribute' => trans('auth.email')]),
        'email.lowercase' => trans('validation.lowercase', ['attribute' => trans('auth.email')]),
        'email.email' => trans('validation.email', ['attribute' => trans('auth.email')]),
        'email.max' => trans('validation.max.string', ['attribute' => trans('auth.email'), 'max' => 255]),
        'email.unique' => trans('validation.unique', ['attribute' => trans('auth.email')]),
    ];
}

}