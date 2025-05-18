<?php

namespace App\Repositories;

use App\Models\StaticPage;
use Spatie\TranslationLoader\LanguageLine;
class StaticPageRepository
{
    protected $model;

    public function __construct(StaticPage $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

public function findBySlug($slug)
{
    return  $this->model->where('slug', $slug)->first();

}
public function findBySlugApis($slug)
{
    $page = $this->model->where('slug', $slug)->first();

    if ($slug === 'about-us' && $page) {
        $additionalKeys = [
            'mission_title_en', 'mission_title_ar',
            'mission_description_en', 'mission_description_ar',
            'vision_title_en', 'vision_title_ar',
            'vision_description_en', 'vision_description_ar',
            'goals_title_en', 'goals_title_ar',
            'goals_description_en', 'goals_description_ar',
            'mission_image', 'vision_image', 'goals_image','about_us_image'
        ];

        $imageKeys = ['mission_image', 'vision_image', 'goals_image','about_us_image'];
        $additionalSettings = \App\Models\Setting::whereIn('key', $additionalKeys)->pluck('value', 'key')->toArray();


        $locale=   request()->Header('Accept-Language');
   
        $transleted_title = LanguageLine::where('key','about_us.main_title')->first();
        $transleted_description = LanguageLine::where('key','about_us.short_description')->first();
                             
  
        foreach ($imageKeys as $key) {
            if (isset($additionalSettings[$key])) {
                $additionalSettings[$key] = env('APP_URL').\Storage::url($additionalSettings[$key]);
            }
        }

        
        $page->additional_settings = $additionalSettings;
        $page->static_titles = [
            'main_title' =>  !empty($transleted_title) ? $transleted_title->text[$locale] : 'no title',
            'short_description' => !empty($transleted_description) ? $transleted_description->text[$locale] : 'no description',
        ];
    }

    return $page;
}


    public function create(array $data)
    {
        $page = new StaticPage();
        $page->slug = str_slug($data['title']);


        $page->translateOrNew('en')->title = $data['title_en'];
        $page->translateOrNew('ar')->title = $data['title_ar'];

        $page->translateOrNew('en')->content = $data['content_en'];
        $page->translateOrNew('ar')->content = $data['content_ar'];

        $page->save();

        return $page;
    }

    public function update(StaticPage $page, array $data)
    {
        $page->translateOrNew('en')->title = $data['title_en'];
        $page->translateOrNew('ar')->title = $data['title_ar'];

        $page->translateOrNew('en')->content = $data['content_en'];
        $page->translateOrNew('ar')->content = $data['content_ar'];

        $page->save();

        return $page;
    }

    public function delete(StaticPage $page)
    {
        return $page->delete();
    }
}