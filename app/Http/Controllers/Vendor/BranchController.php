<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\BranchRequest;
use App\Models\Branch;
use App\Models\City;
use App\Models\Service;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function __construct(protected BranchService $branchService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'services', 'working_days']);
        $branches = $this->branchService->list($request->user()->id, $filters);

        return Inertia::render('Vendor/Branches/Index', [
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

public function create()
{
    $services = Service::where('vendor_id', auth()->id())
        ->get(['id as value', 'name as label'])
        ->toArray();

    $cities = City::get(['id as value', 'name as label'])
        ->toArray();

    return Inertia::render('Vendor/Branches/Create', [
        'services' => $services,
        'cities' => $cities
    ]);
}

    // public function store(branchRequest $request)
    // {
    //     $this->branchService->store($request->validated(), $request->user()->id);

    //     return redirect()->route('vendor.branches.index')
    //         ->with('success', __('Branch added successfully.'));
    // }

// public function store(BranchRequest $request)
// {
//     try {
//         $validated = $request->validated();
//         // Convert working_days and services to proper arrays
//         $validated['working_days'] = array_map('intval', $validated['working_days']);
//         $validated['services'] = array_map('intval', $validated['services']);

//         // Process working_hours to maintain structure
//         $workingHours = [];
//         foreach ($validated['working_days'] as $day) {
//             if (isset($validated['working_hours'][$day])) {
//                 $workingHours[$day] = [
//                     'open' => $validated['working_hours'][$day]['open'],
//                     'close' => $validated['working_hours'][$day]['close']
//                 ];
//             }
//         }
//         $validated['working_hours'] = $workingHours;

//         // Create branch with processed data
//         $branch = $this->branchService->create(
//             array_merge($validated, ['vendor_id' => $request->user()->id]),
//             $request->file('images', [])
//         );

//         return redirect()->route('vendor.branches.index')
//             ->with('success', __('Branch created successfully'));

//     } catch (\Exception $e) {
//         return back()->withInput()
//             ->with('error', __('Failed to create branch: ') . $e->getMessage());
//     }
// }

public function store(BranchRequest $request)
{
    try {
        // Manually decode JSON fields
        $data = $request->all();

        $jsonFields = ['working_days', 'working_hours', 'services'];
        foreach ($jsonFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = is_string($data[$field])
                    ? json_decode($data[$field], true)
                    : $data[$field];
            }
        }

        // Create branch with processed data
        $branch = Branch::create([
            'vendor_id' => $request->user()->id,
            'name' => $data['name'],
            'city_id' => $data['city_id'],
            'working_days' => $data['working_days'],
            'working_hours' => $data['working_hours'],
            'services' => $data['services']
        ]);
return $data;

        // Handle image uploads
        if ($request->hasFile('images')) {
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
            ->with('success', __('Branch created successfully'));

    } catch (\Exception $e) {
        return back()->withInput()
            ->with('error', __('Failed to create branch: ') . $e->getMessage());
    }
}
    public function edit(Branch $branch)
    {
        $this->authorize('update', $branch);

        return Inertia::render('Vendor/Branches/Edit', [
            'branch' => $branch->load('images'),
        ]);
    }

    public function update(BranchRequest $request, Branch $branch)
    {
        $this->authorize('update', $branch);

        $this->branchService->update($branch, $request->validated());

        return redirect()->route('vendor.branches.index')
            ->with('success', __('Branch details updated successfully.'));
    }

    public function destroy(Branch $branch)
    {
        $this->authorize('delete', $branch);

        if (!$this->branchService->canDelete($branch)) {
            return back()->with('error', __('This branch cannot be deleted due to active appointments.'));
        }

        $this->branchService->delete($branch);

        return back()->with('success', __('Branch deleted successfully.'));
    }

    public function toggleStatus(Branch $branch)
    {
        $this->authorize('update', $branch);

        $this->branchService->toggleStatus($branch);

        return back()->with('success', __('Branch status updated successfully.'));
    }
}
