<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
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
    public function rules()
    {
        $rules = [
            // 'name' => 'required|string|max:255|unique:roles,name', // Validate name field
        ];

        foreach (config('app.supported_languages') as $language) {
            $rules["translations.{$language}.name"] = 'required|string|max:255';
        }

        return $rules;

    }

    public function messages()
    {

        $messages = [];
        foreach (config('app.supported_languages') as $language) {
            $messages["translations.{$language}.name.required"] = __('rules.Role_name_is_required') . " ({$language})";
            $messages["translations.{$language}.name.unique"] = __('rules.This_role_name_has_already_been_taken') . " ({$language})";
            $messages["translations.{$language}.name.string"] = __('rules.Role_name_must_be_a_string') . " ({$language})";
            $messages["translations.{$language}.name.max"] = __('rules.Role_name_must_not_exceed_255_characters') . " ({$language})";
        }

        return $messages;

    }
}