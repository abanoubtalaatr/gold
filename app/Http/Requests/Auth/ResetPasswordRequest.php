<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
        public function rules()
        {
        return [
        'email' => 'required|email|exists:accounts,email',
        'password' => [
                            'required',
                            'string',
                            'min:6',
                            'confirmed',
                            'regex:/[A-Z]/', // must contain at least one uppercase letter
                            'regex:/[@$!%*?&#]/' // must contain at least one special character
                        ]
        ];
        }
}