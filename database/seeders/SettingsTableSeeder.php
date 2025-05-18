<?php

namespace  Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{

    public function run()
    {
        Setting::truncate();

        $settings = [

            ['key' => 'facebook', 'value' => 'http://facebook.com', 'group' => 'social', 'type' => 'url'],
            ['key' => 'linkedIn', 'value' => 'http://inkedin.com', 'group' => 'social', 'type' => 'url'],
            // ['key' => 'instagram', 'value' => 'http://instagram.com', 'group' => 'social', 'type' => 'url'],
            // ['key' => 'snapchat', 'value' => 'http://snapchat.com', 'group' => 'social', 'type' => 'url'],
            ['key' => 'youtube', 'value' => 'http://youtube.com', 'group' => 'social', 'type' => 'url'],
            ['key' => 'twitter', 'value' => 'http://twitter.com', 'group' => 'social', 'type' => 'url'],
            ['key' => 'address_ar', 'value' => '', 'group' => 'contact',  'type' => 'text'],
            ['key' => 'address_en', 'value' => '', 'group' => 'contact', 'type' => 'text'],

            ['key' => 'footer_txt_ar', 'value' => ' هنالك العديد من الأنواع المتوفرة لنصوص لوريم إيبسوم، ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر أو الكلمات العشوائية إلى النص. إن كنت تريد أن تستخدم نص لوريم إيبسوم ما، عليك أن تتحقق أولاً أن ليس هناك أي كلمات أو عبارات محرجة أو غير لائقة مخبأة في هذا النص. ', 'group' => 'notes',  'type' => 'textarea'],
            ['key' => 'footer_txt_en', 'value' => ' هنالك العديد من الأنواع المتوفرة لنصوص لوريم إيبسوم، ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر أو الكلمات العشوائية إلى النص. إن كنت تريد أن تستخدم نص لوريم إيبسوم ما، عليك أن تتحقق أولاً أن ليس هناك أي كلمات أو عبارات محرجة أو غير لائقة مخبأة في هذا النص. ', 'group' => 'notes',  'type' => 'textarea'],

            ['key' => 'contact_txt_ar', 'value' => ' هنالك العديد من الأنواع المتوفرة لنصوص لوريم إيبسوم، ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر أو الكلمات العشوائية إلى النص. إن كنت تريد أن تستخدم نص لوريم إيبسوم ما، عليك أن تتحقق أولاً أن ليس هناك أي كلمات أو عبارات محرجة أو غير لائقة مخبأة في هذا النص. ', 'group' => 'notes',  'type' => 'textarea'],
            ['key' => 'contact_txt_en', 'value' => ' هنالك العديد من الأنواع المتوفرة لنصوص لوريم إيبسوم، ولكن الغالبية تم تعديلها بشكل ما عبر إدخال بعض النوادر أو الكلمات العشوائية إلى النص. إن كنت تريد أن تستخدم نص لوريم إيبسوم ما، عليك أن تتحقق أولاً أن ليس هناك أي كلمات أو عبارات محرجة أو غير لائقة مخبأة في هذا النص. ', 'group' => 'notes',  'type' => 'textarea'],

            ['key' => 'mobile', 'value' => '+966553113265', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'email', 'value' => 'info@example.com', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'whatsapp', 'value' => '+966553113265', 'group' => 'contact', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
