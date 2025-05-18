<?php

namespace App\Http\Controllers\Partners;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreRequest;
use App\Http\Requests\Partner\UpdateRequest;
use App\Models\Partner;
use App\Repositories\PartnerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    private $repository;

    public function __construct(PartnerRepository $repository)
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

        $items = $this->repository->getFilteredpartners($filters);

        return Inertia('Partner/Index', [
            'items' => $items->load('translations'),
            'filters' => $filters,

        ]);
    }

    public function create()
    {
        return Inertia('Partner/Create');
    }

    public function store(StoreRequest $request)
    {
        try {
            $partner = $this->repository->create($request);
            return redirect()
                ->route('Partner.index')
                ->with('success', __('messages.data_created_successfully'));
        } catch (\Exception $e) {
            \Log::error('Error creating main service: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Partner $partner)
    {
        return Inertia('Partner/Show', [
            'partner' => $partner]);
    }


    public function edit(Partner $partner)
    {
        return inertia('Partner/Edit', [
            'partner' => $partner->load('translations')
        ]);
    }

    public function update(UpdateRequest $request, Partner $partner)
    {
        try {
            $this->repository->update($partner, $request);
            return redirect()->route('partners.index')->with('success', __('messages.data_updated_successfully'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Partner $partner)
    {
        try {
            $this->repository->delete($partner);
            return redirect()
                ->route('partners.index')
                ->with('success', __('messages.data_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function activate(Partner $partner)
    {
        $partner->update(['is_active' => !$partner->is_active]);
        return redirect()->route('partners.index')
            ->with('success', __('messages.status_updated'));
    }
}
