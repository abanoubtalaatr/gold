<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
class VerifyEmailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:accounts,email',
            'otp' => 'required|string',
        ];
    }
        public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $email = $this->input('email');

            $exists = \DB::table('accounts')
                ->where('email', $email)
                ->orWhere('pending_email', $email)
                ->exists();

            if (!$exists) {
                $validator->errors()->add('email', __('messages.email_not_found'));
            }
        });
    }
}