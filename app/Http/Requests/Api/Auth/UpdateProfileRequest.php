<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\CustomResponse;

class UpdateProfileRequest extends FormRequest
{
    use CustomResponse;
    
    public function rules(): array
    {

        return [
            'type' => 'required|in:user,company,employee',
            'name' => ['required', 'string', 'max:200'],
            'avatar' => 'nullable|image|mimes:jpg,png,jpeg|max:20480',
            'id_number' => ['required','starts_with:1,2','numeric','min:10',Rule::unique('users', 'id_number')->where(function ($query) {
                return $query->first();
            })->ignore(auth('api')->id())],
            'email' => ['required', 'string', 'email', 'max:255',Rule::unique('users', 'email')->where(function ($query) {
                return $query->first();
            })->ignore(auth('api')->id())],
            'mobile' => ['required', 'string', 'regex:/[0-9]/', Rule::unique('users', 'mobile')->where(function ($query) {
                $q = $query
                    ->where('dialling_code', request()->input('dialling_code'))
                    ;
                return $q->first();
            })->ignore(auth('api')->id())],
            'dialling_code' => 'required',
            'cmrcial_rgstr_nmbr' => ['nullable', 'required_if:type,company', 'string', 'max:255'],
            'tax_number' => ['nullable', 'required_if:type,company', 'string', 'max:255'],
            'unified_number' => ['nullable', 'required_if:type,company', 'string',Rule::unique('users', 'unified_number')->where(function ($query) {
                return $query->first();
            })->ignore(auth('api')->id())],
        ];
    }
}
