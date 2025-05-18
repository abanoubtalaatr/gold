<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {


            $rules = [];

            foreach (config('app.supported_languages') as $language) {
            $rules ["translations.{$language}.name"] = ['required',
                'string',
                'max:255',
                'unique:roles,name,' . $this->role->id] ;
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