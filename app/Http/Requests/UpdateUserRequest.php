<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
           // dd($this->all());
        $rules = [
            'email' => 'required|email|unique:users,email,' . $this->route('user')->id,
            'name' => 'required|string|max:225',
            'avatar' => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',
            'selectedRoles' => 'nullable|array',
            'selectedRoles.*' => 'exists:roles,name',
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            
        ];

         switch ($this->method()) {
                    case 'POST':
                        $rules['password'] = [
                          $this->route('user')->id ? 'nullable' :  'required',
                            "nullable",
                            'string',
                            'min:6',
                            'confirmed',
                        
                        ];
                        break;
                    case 'PUT':
                    case 'PATCH':
                        $rules['password'] = [
                            'nullable',
                            'string',
                            'min:6',
                            'confirmed',
                            'regex:/[A-Z]/', // must contain at least one uppercase letter
                            'regex:/[@$!%*?&#]/' // must contain at least one special character
                        ];
                        break;
                }

                return $rules;
    }

    public function messages()
    {
        $messages = [
            'email.required' => __('rules.Email_is_required'),
            'email.email' => __('rules.Email_must_be_a_valid_email_address'),
            'email.unique' => __('rules.This_email_has_already_been_taken'),
            'password.required' => __('rules.Password_is_required'),
            'password.min' => __('rules.Password_must_be_at_least_8_characters'),
            'avatar.image' => __('rules.The_avatar_must_be_an_image_file'),
            'avatar.mimes' => __('rules.The_avatar_must_be_a_file_of_type_jpeg_png_jpg_gif'),
            'avatar.max' => __('rules.The_avatar_must_not_exceed_2MB'),
        ];

        return $messages;

    }

}