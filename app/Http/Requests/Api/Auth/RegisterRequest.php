<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\CustomResponse;

class RegisterRequest extends FormRequest
{
    use CustomResponse;
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'mobile' => ['required', 'string', 'regex:/[0-9]/', 'min:9', 'max:9', Rule::unique('users', 'mobile')->where(function ($query) {
                return $query->where('dialling_code', request()->input('dialling_code'))->first();
            })],
            'dialling_code' => 'required',
            'password' => [
                'min:8',
                'required',
                'confirmed',
                'string',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'accept_terms' => ['required', 'boolean'],
        ];
    }
}
