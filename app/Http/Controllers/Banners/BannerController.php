<?php

namespace App\Http\Controllers\Banners;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreRequest;
use App\Http\Requests\Banner\UpdateRequest;
use App\Models\Banner;
use App\Repositories\BannerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BannerController extends Controller
{
    private $repository;

    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $filters = [
            'title' => $request->title,
            'is_active' => $request->is_active,
            'sort_order' => $request->sort_order
        ];

        $items = $this->repository->getFilteredBanners($filters);

        return Inertia('Banner/Index', [
            'items' => $items,
            'filters' => $filters,

        ]);
    }

    public function create()
    {
        return Inertia('Banner/Create');
    }

    public function store(StoreRequest $request)
    {
        $this->repository->create($request);
        
        return redirect()
            ->route('banners.index')
            ->with('success', __('messages.data_created_successfully'));
    }

    public function show(Banner $banner)
    {
        return Inertia('Banner/Show', [
            'banner' => $banner
        ]);
    }


    public function edit(Banner $banner)
    {
        return inertia('Banner/Edit', [
            'banner' => $banner->load('translations')
        ]);
    }

    public function update(UpdateRequest $request, Banner $Banner)
    {
        try {

            $this->repository->update($Banner, $request);
            return redirect()->route('banners.index')->with('success', __('messages.data_updated_successfully'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Banner $banner)
    {
        try {
            $this->repository->delete($banner);
            return redirect()
                ->route('banners.index')
                ->with('success', __('messages.data_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function activate(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return redirect()->route('banners.index')
            ->with('success', __('messages.status_updated'));
    }
}
