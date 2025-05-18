<?php

namespace  App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\CustomResponse;

/**
 * Class ResetPasswordRequest.
 */
class ResetPasswordRequest extends FormRequest
{
    use CustomResponse;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['required'],
            'email' => ['required_without:mobile'],
            'mobile' => ['required_without:email'],
            'dialling_code' => ['required_without:email'],
            'password' => [
                'required',
                'confirmed',
                'min:8','string',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        ];
    }
}