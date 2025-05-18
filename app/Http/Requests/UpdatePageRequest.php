<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()

{
    return [
        'image' => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',
        'translations' => 'required|array',
        'translations.*.title' => 'required|string|max:255',
        'translations.*.description' => 'required|string',
        'sections' => 'nullable|array',
        'sections.*.id' => 'nullable|exists:page_sections,id',
        'sections.*.type' => 'required|string',
        'sections.*.image' => 'nullable|image|mimes:webp,jpeg,png,jpg,gif|max:2048',
        'sections.*.translations' => 'required|array',
        'sections.*.translations.*.title' => 'required|string|max:255',
        'sections.*.translations.*.description' => 'nullable|string',
    ];

    }
}