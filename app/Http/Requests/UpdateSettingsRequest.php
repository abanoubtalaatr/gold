<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'whatsapp' => ['required', 'string', 'max:15',  'regex:/^\+9665[0-9]{8}$/'],
            'email' => ['required', 'email'],
            'mobile' => ['required'],
            'address_ar' => ['required', 'string', 'min:10', 'max:255'],
            'address_en' => ['nullable', 'string', 'min:10', 'max:255'],
            'facebook' => ['nullable', 'url', 'string', 'min:10', 'max:255'],
            'twitter' => ['nullable', 'url', 'string', 'min:10', 'max:255'],
            'instagram' => ['nullable', 'url', 'string', 'min:10', 'max:255'],
            'youtube' => ['nullable', 'url', 'string', 'min:10', 'max:255'],
            'linkedIn' => ['nullable', 'url', 'string', 'min:10', 'max:255'],
            'snapchat' => ['nullable', 'url', 'string', 'min:10', 'max:255'],
            'footer_txt_ar' => ['required', 'string', 'min:10', 'max:500'],
            'footer_txt_en' => ['nullable', 'string', 'min:10', 'max:500'],
            'contact_txt_ar' => ['required', 'string', 'min:10', 'max:500'],
            'contact_txt_en' => ['nullable', 'string', 'min:10', 'max:500'],
        ];
    }

    public function attributes()
    {
        return [
            'whatsapp' => __('messages.whatsapp'),
            'email' => __('messages.email'),
            'address' => __('messages.address'),
            'about_us_image' => __('messages.about_us_image'),
            'service_image' => __('messages.service_image'),
            'contact_us_page_title_en' => __('messages.contact_us_page_title_en'),
            'contact_us_page_title_ar' => __('messages.contact_us_page_title_ar'),
            'address_on_map' => __('messages.address_on_map'),
            'facebook' => __('messages.facebook'),
            'twitter' => __('messages.twitter'),
            'instagram' => __('messages.instagram'),
            'youtube' => __('messages.youtube'),
            'snapchat' => __('messages.snapchat'),
            'linkedIn' => __('messages.linkedIn'),
            'session_lifetime' => __('messages.session_lifetime'),
            'vat_rate' => __('messages.vat_rate'),
            'commission_type' => __('messages.commission_type'),
            'commission_value' => __('messages.commission_value'),
            'footer_txt_ar' => __('messages.footer_txt_ar'),
            'footer_txt_en' => __('messages.footer_txt_en'),
            'contact_txt_ar' => __('messages.contact_txt_ar'),
            'contact_txt_en' => __('messages.contact_txt_en'),
        ];
    }
}
