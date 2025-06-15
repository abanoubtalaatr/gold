<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'image' => public_path('banner.png'),
                'is_active' => true,
                'translations' => [
                    'ar' => ['title' => 'العرض الأول'],
                    'en' => ['title' => 'First Banner'],
                ],
            ],
            [
                'image' => public_path('banner.png'),
                'is_active' => true,
                'translations' => [
                    'ar' => ['title' => 'العرض الثاني'],
                    'en' => ['title' => 'Second Banner'],
                ],
            ],
        ];

        foreach ($banners as $banner) {
            $translations = $banner['translations'];
            unset($banner['translations']);

            $bannerModel = Banner::create($banner);

            foreach ($translations as $locale => $translation) {
                $bannerModel->translateOrNew($locale)->fill($translation);
            }

            $bannerModel->save();
        }
    }
}
