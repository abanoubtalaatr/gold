<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\BranchRequest;
use App\Models\Branch;
use App\Models\City;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function __construct(protected BranchService $branchService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        
        $branches = $this->branchService->list($request->user()->id, $filters);

        return Inertia::render('Vendor/Branches/Index', [
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $cities = City::select(['id as value', 'name as label'])->limit(10)->get()->toArray();

        return Inertia::render('Vendor/Branches/Create', [
            'cities' => $cities,
        ]);
    }

    public function store(BranchRequest $request)
    {
        // try {
            $validated = $request->validated();

            // Ensure working_days is an array of integers
            $validated['working_days'] = array_map('intval', $validated['working_days']);

            // Create branch
            $branch = Branch::create([
                'vendor_id' => $request->user()->id,
                'name' => $validated['name'],
                'city_id' => $validated['city_id'],
                'working_days' => $validated['working_days'],
                'working_hours' => $validated['working_hours'],
                'address' => 'address',
                'is_active' => true,
            ]);

            // Log branch creation
            Log::info('Branch created', ['branch_id' => $branch->id, 'vendor_id' => $request->user()->id]);

            // Handle image uploads
            

            return redirect()->route('vendor.branches.index')
                ->with('success', __('Branch created successfully'));

        // } catch (\Exception $e) {
        //     Log::error('Failed to create branch', ['error' => $e->getMessage()]);
        //     return back()->withInput()
        //         ->with('error', __('Failed to create branch: ') . $e->getMessage());
        // }
    }

    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);

        return Inertia::render('Vendor/Branches/Edit', [
            'branch' => $branch,
            'cities' => City::select(['id as value', 'name as label'])->get()->toArray(),
        ]);
    }

    public function update(BranchRequest $request, Branch $branch)
    {
        $this->authorize('update', $branch);

        try {
            $validated = $request->validated();
            $validated['working_days'] = array_map('intval', $validated['working_days']);

            $branch->update([
                'name' => $validated['name'],
                'city_id' => $validated['city_id'],
                'working_days' => $validated['working_days'],
                'working_hours' => $validated['working_hours'],
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($branch->images as $image) {
                    \Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
                foreach ($request->file('images') as $image) {
                    $path = $image->store('branches', 'public');
                    $branch->images()->create([
                        'path' => $path,
                        'name' => $image->getClientOriginalName(),
                        'type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                    ]);
                }
            }

            return redirect()->route('vendor.branches.index')
                ->with('success', __('Branch updated successfully'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('Failed to update branch: ') . $e->getMessage());
        }
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('delete', $branch);

        if (!$this->branchService->canDelete($branch)) {
            return back()->with('error', __('This branch cannot be deleted due to active appointments.'));
        }

        foreach ($branch->images as $image) {
            \Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $branch->delete();

        return back()->with('success', __('Branch deleted successfully'));
    }

    public function toggleStatus(Branch $branch)
    {
        $this->authorize('update', $branch);

        $branch->update(['is_active' => !$branch->is_active]);

        return back()->with('success', __('Branch status updated successfully'));
    }
}