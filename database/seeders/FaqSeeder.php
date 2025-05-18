<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run()
    {
        $faqs = [
            [
                'type' => 'general',
                'is_active' => true,
                'translations' => [
                    'en' => [
                        'question' => 'What is our return policy?',
                        'answer' => 'Our return policy lasts 30 days from the date of purchase.',
                    ],
                    'ar' => [
                        'question' => 'ما هي سياسة الاسترجاع لدينا؟',
                        'answer' => 'سياسة الاسترجاع لدينا تستمر لمدة 30 يومًا من تاريخ الشراء.',
                    ],
                ],
            ],
            [
                'type' => 'general',
                'is_active' => true,
                'translations' => [
                    'en' => [
                        'question' => 'How can I reset my password?',
                        'answer' => 'To reset your password, click on the "Forgot Password" link on the login page.',
                    ],
                    'ar' => [
                        'question' => 'كيف يمكنني إعادة تعيين كلمة المرور؟',
                        'answer' => 'لإعادة تعيين كلمة المرور، انقر فوق رابط "نسيت كلمة المرور" في صفحة تسجيل الدخول.',
                    ],
                ],
            ],
        ];

        foreach ($faqs as $faqData) {
            $faq = Faq::create([
                'type' => $faqData['type'],
                'is_active' => $faqData['is_active'],
            ]);

            foreach ($faqData['translations'] as $locale => $translation) {
                $faq->translations()->create([
                    'locale' => $locale,
                    'question' => $translation['question'],
                    'answer' => $translation['answer'],
                ]);
            }
        }
    }
}
