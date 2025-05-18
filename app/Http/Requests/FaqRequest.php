<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'translations' => 'required|array',
            'translations.ar' => 'required|array',
            'translations.en' => 'required|array',
            'translations.*.question' => 'required|string|max:255',
            'translations.*.answer' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'required|numeric|min:0|unique:faqs,sort_order',
        ];
    }

    public function attributes()
    {
        return [
            'translations.ar.question' => __('messages.question') . ' ' . __('messages.arabic'),
            'translations.ar.answer' => __('messages.answer') . ' ' . __('messages.arabic'),
            'translations.en.question' => __('messages.question') . ' ' . __('messages.english'),
            'translations.en.answer' => __('messages.answer') . ' ' . __('messages.english'),
        ];
    }

    public function messages()
    {
        return [
            'translations.ar.question.required' => __('messages.question_required_ar'),
            'translations.ar.answer.required' => __('messages.answer_required_ar'),
            'translations.en.question.required' => __('messages.question_required_en'),
            'translations.en.answer.required' => __('messages.answer_required_en'),
            'translations.*.question.max' => __('messages.question_max_length'),
        ];
    }
}