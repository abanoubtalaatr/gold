<?php

namespace App\Repositories;

use App\Models\Page;
use App\Services\FileUploadService;

class PageRepository implements PageRepositoryInterface
{
    public function __construct(protected FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getAll()
    {
        return Page::with('translations')->get();
    }

    public function getBySlug(string $slug, $locale)
    {
        return Page::with('translations')->where('slug', $slug)->firstOrFail();
    }

    public function getPageWithSections(string $slug)
    {
        return Page::with('translations', 'sections', 'sections.translations')->where('slug', $slug)->firstOrFail();
    }

    public function updatePage($request, $pageId)
    {
        $page = Page::findOrFail($pageId);
        $data = $request->all();

        if (isset($data['translations'])) {
            foreach ($data['translations'] as $locale => $translation) {
                $page->translateOrNew($locale)->title = $translation['title'];
                $page->translateOrNew($locale)->description = $translation['description'];
            }
            $page->save(); // حفظ الترجمات
        }

        if ($request->hasFile('image')) {
            if ($page->image && \Storage::disk('public')->exists($page->image)) {
                $this->fileUploadService->deleteFile($page->image);
            }
            $data['image'] = $request->file('image')->store('pages', 'public');
        } else {
            unset($data['image']);
        }

        if (isset($data['sections'])) {
            foreach ($data['sections'] as $sectionData) {
                $section = $page->sections()->updateOrCreate(
                    ['id' => $sectionData['id'] ?? null],
                    [
                        'type' => $sectionData['type'] ?? null,
                    ]
                );

                if (isset($sectionData['image']) && $sectionData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    if ($section->image) {
                        \Storage::disk('public')->delete($section->image);
                    }
                    $sectionData['image'] = $sectionData['image']->store('sections', 'public');
                    $section->image = $sectionData['image'];
                    $section->save();
                }

                if (isset($sectionData['translations'])) {
                    $section->translations()->delete();

                    foreach ($sectionData['translations'] as $locale => $translation) {
                        if (in_array($locale, ['ar', 'en']) && isset($translation['title'])) {
                            $section->translateOrNew($locale)->fill([
                                'title' => $translation['title'],
                                'description' => $translation['description'] ?? ''
                            ]);
                        }
                    }
                    $section->save(); // حفظ ترجمات القسم
                }
            }
        }

        // تحديث بيانات الصفحة
        $page->update(collect($data)->except(['translations', 'sections'])->toArray());

        return $page->fresh(['translations', 'sections', 'sections.translations']);
    }
}