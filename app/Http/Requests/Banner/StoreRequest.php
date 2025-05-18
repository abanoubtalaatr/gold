<?php

namespace App\Http\Requests\Banner;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     */
    public function rules(): array
    {        //dd($this->image);

        $rules = [
        'image' => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',
        'sort_order' => 'required|numeric|unique:banners,sort_order',
        ];



        foreach (config('app.supported_languages') as $language) {
            $nameRules = [
                'required',
                'string',
                'min:20',
                'max:255',
            ];

            $descriptionRules = [
                'required',
                'string',
                'min:20',
                'max:500',
            ];


            if ($language === 'ar') {
                $nameRules[] = function($attribute, $value, $fail) {
                    if (!preg_match('/^[\p{Arabic}\p{N}\p{P}\p{Z}\s]+$/u', $value)) {
                        $fail(__('messages.arabic_text_only'));
                    }
                };
                $descriptionRules[] = function($attribute, $value, $fail) {
                    $plainText = strip_tags($value);
                    if (!preg_match('/[\p{Arabic}]/u', $plainText)) {
                        $fail(__('messages.arabic_text_only'));
                    }
                };
            } elseif ($language === 'en') {
                $nameRules[] = function($attribute, $value, $fail) {
                    if (!preg_match('/^[a-zA-Z0-9\s\-\_]+$/', $value)) {
                        $fail(__('messages.english_text_only'));
                    }
                };
                $descriptionRules[] = function($attribute, $value, $fail) {
                    if (!preg_match('/^[a-zA-Z0-9\s\-\_]+$/', $value)) {
                        $fail(__('messages.english_text_only'));
                    }
                };
            }

            $rules["translations.{$language}.title"] = $nameRules;
            $rules["translations.{$language}.description"] = $descriptionRules;
        }


        return $rules;

    }

    /**
     * Get custom messages for validator errors.
     */

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => __('messages.title'),
            'translations.en.title' => __('messages.title_en'),
            'translations.ar.title' => __('messages.title_ar'),
            'translations.en.description' => __('messages.description_en'),
            'translations.ar.description' => __('messages.description_ar'),
            'sort_order' => __('messages.sort_order'),
            'image' => __('messages.image'),
            'is_active' => __('messages.is_active')
        ];
    }
}