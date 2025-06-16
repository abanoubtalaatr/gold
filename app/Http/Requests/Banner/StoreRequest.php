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
    {
        $rules = [
            'image' => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ];

        foreach (config('app.supported_languages') as $language) {
            
            $rules["translations.{$language}.description"] = [
                'required',
                'string',
                'min:3',
                'max:500',
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'translations.*.title.required' => __('messages.title_required_all_languages'),
            'translations.ar.title.required' => __('messages.title_ar_required'),
            'translations.en.title.required' => __('messages.title_en_required'),
            'translations.*.title.min' => __('messages.title_min_length'),
            'translations.*.description.min' => __('messages.description_min_length'),
            'translations.*.description.max' => __('messages.description_max_length'),
            'image.image' => __('messages.file_must_be_image'),
            'image.mimes' => __('messages.image_format_invalid'),
            'image.max' => __('messages.image_size_too_large'),
            'sort_order.unique' => __('messages.sort_order_unique'),
        ];
    }

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