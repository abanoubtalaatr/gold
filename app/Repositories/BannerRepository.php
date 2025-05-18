<?php

namespace App\Repositories;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BannerRepository
{
    protected $model;

    public function __construct(Banner $model)
    {
        $this->model = $model;
    }

    public function query()
    {
        return $this->model->newQuery();
    }

    public function all()
    {
        return cache()->remember('banner.all', 3600, function () {
            return $this->model->all();
        });
    }

    public function find($id)
    {
        return cache()->remember('banner.' . $id, 3600, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'is_active' => $request->boolean('is_active'),
                'image' => null,
                'is_active' => true,
                'sort_order' => $request->sort_order
            ];

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('banners', 'public');
                $data['image'] = $path;
            }

            $banner = $this->model->create($data);

            if ($request->has('translations')) {
                foreach ($request->input('translations') as $locale => $translation) {
                    $banner->translateOrNew($locale)->title = $translation['title'];
                }
                $banner->save();
            }


            DB::commit();

            return $banner;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in banner create: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update($banner, $request)
    {
        DB::beginTransaction();
        try {
            if ($request->has('translations')) {
                foreach ($request->input('translations') as $locale => $translation) {
                    $banner->translateOrNew($locale)->title = $translation['title'];
                    $banner->translateOrNew($locale)->description = $translation['description'];
                }
            }

            if ($request->hasFile('image')) {
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }

                $path = $request->file('image')->store('banners', 'public');
                $banner->image = $path;
            }

            $banner->sort_order = $request->sort_order;
            $banner->is_active = true;


            $banner->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating banner: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getFilteredBanners($filters)
    {
        $query = $this->model->query()->with('translations');

        if ($filters['title']) {
            $query->whereTranslationLike('title', "%{$filters['title']}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate();
    }

    public function delete($model)
    {
        $model->delete();
    }
}