<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GetOtpRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:accounts,email',
        ];
    }
}