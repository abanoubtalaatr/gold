<?php


namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Database\Seeders\Traits\TruncateTable;
use Database\Seeders\Traits\DisableForeignKeys;

class PageSeeder extends Seeder
{
    use TruncateTable,DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();
        $this->truncate('pages');
        $this->truncate('page_sections');
        $this->truncate('page_section_translations');
        $this->truncate('page_translations');
        $this->enableForeignKeys();
        
        $pages = [
            [
                'slug' => 'about-us',
                'image' => 'pages/about-us.jpg',
                'translations' => [
                    'en' => [
                        'title' => 'About Us',
                        'description' => 'This is the description of the about us page in English.',
                    ],
                    'ar' => [
                        'title' => 'عن الموقع',
                        'description' => 'هذا هو وصف صفحة عن الموقع باللغة العربية.',
                    ],
                ],
                'sections' => [
                    [
                        'type' => 'vision',
                        'image' => 'pages/vision.jpg',
                        'translations' => [
                            'en' => [
                                'title' => 'Our Vision',
                                'description' => 'This is the vision description in English.',
                            ],
                            'ar' => [
                                'title' => 'رؤيتنا',
                                'description' => 'هذا هو وصف الرؤية باللغة العربية.',
                            ],
                        ],
                    ],
                    [
                        'type' => 'mission',
                        'image' => 'pages/mission.jpg',
                        'translations' => [
                            'en' => [
                                'title' => 'Our Mission',
                                'description' => 'This is the mission description in English.',
                            ],
                            'ar' => [
                                'title' => 'مهمتنا',
                                'description' => 'هذا هو وصف المهمة باللغة العربية.',
                            ],
                        ],
                    ],
                    [
                        'type' => 'objectives',
                        'image' => 'pages/objectives.jpg',
                        'translations' => [
                            'en' => [
                                'title' => 'Our Objectives',
                                'description' => 'These are the objectives in English.',
                            ],
                            'ar' => [
                                'title' => 'أهدافنا',
                                'description' => 'هذه هي أهدافنا باللغة العربية.',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'privacy-policy',
                'image' => 'pages/privacy-policy.jpg',
                'translations' => [
                    'en' => [
                        'title' => 'Privacy Policy',
                        'description' => 'This is the description of the privacy policy in English.',
                    ],
                    'ar' => [
                        'title' => 'سياسة الخصوصية',
                        'description' => 'هذا هو وصف صفحة سياسة الخصوصية باللغة العربية.',
                    ],
                ],
            ],
            [
                'slug' => 'terms-and-conditions',
                'image' => 'pages/terms-and-conditions.jpg',
                'translations' => [
                    'en' => [
                        'title' => 'Terms and Conditions',
                        'description' => 'This is the description of the terms and conditions in English.',
                    ],
                    'ar' => [
                        'title' => 'الشروط والأحكام',
                        'description' => 'هذا هو وصف صفحة الشروط والأحكام باللغة العربية.',
                    ],
                ],
            ],
            
        ];

        foreach ($pages as $pageData) {
            $page = Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                ['image' => $pageData['image']]
            );

            foreach ($pageData['translations'] as $locale => $translation) {
                $page->translateOrNew($locale)->fill($translation);
            }
            $page->save();

            if ($pageData['slug'] === 'about-us' && isset($pageData['sections'])) {
                foreach ($pageData['sections'] as $sectionData) {
                    $section = $page->sections()->updateOrCreate(
                        ['type' => $sectionData['type']],
                        ['image' => $sectionData['image']]
                    );

                    foreach ($sectionData['translations'] as $locale => $translation) {
                        $section->translateOrNew($locale)->fill($translation);
                    }
                    $section->save();
                }
            }
        }
    }
}
