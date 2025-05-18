<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['nullable', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable',  'max:255'],
            'message' => ['required', 'max:500'],
            'subject' => ['required', 'max:500'],
        ];
    }


    public function attributes()
    {
        return [
            'name' => trans('messages.name'),
            'email' => trans('messages.email'),
            'phone' => trans('messages.phone'),
            'message' => trans('messages.message'),

        ];
    }
}