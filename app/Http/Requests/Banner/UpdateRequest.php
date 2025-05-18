<?php

namespace App\Http\Requests\Banner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'required|numeric|unique:banners,sort_order,' . $this->route('banner')->id,

            'is_active' => 'boolean'
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

    public function messages(): array
    {
        return [
            'translations.*.title.required' => 'الاسم مطلوب لجميع اللغات',
            'translations.ar.title.required' => 'الاسم باللغة العربية مطلوب',
            'translations.en.title.required' => 'الاسم باللغة الإنجليزية مطلوب',
            'translations.*.title.min' => 'الاسم يجب أن يكون أكثر من 20 حرف',
            'translations.*.description.min' => 'الوصف يجب أن يكون أكثر من 20 حرف',
            'translations.*.description.max' => 'الوصف يجب أن يكون أقل من 500 حرف',
            'translations.*.description.required' => 'الوصف مطلوب لجميع اللغات',
            'translations.ar.description.required' => 'الوصف باللغة العربية مطلوب',
            'translations.en.description.required' => 'الوصف باللغة الإنجليزية مطلوب',
            'image.image' => 'يجب أن يكون الملف المرفق صورة',
            'image.mimes' => 'يجب أن تكون الصورة من نوع: jpeg, png, jpg, gif',
            'image.max' => 'حجم الصورة لا يجب أن يتجاوز 2 ميجابايت'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => (bool) $this->is_active,
            ]);
        }
    }
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