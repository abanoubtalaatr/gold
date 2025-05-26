<?php

namespace App\Http\Controllers\Vendor;

use App\Models\City;
use Inertia\Inertia;
use App\Models\State;
use App\Models\Branch;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Services\BranchService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Vendor\BranchRequest;

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
        //saudi arabia country id is 194
        
        $country = Country::find(194);
        
        // i want to get all cities for this all states for this country  
        $states = State::where('country_id', $country->id)->pluck('id')->toArray();
        $cities = City::select(['id as value', 'name as label'])->limit(10)->get()->toArray();

        // $cities = City::whereIn('state_id',$states)->get()->toArray();
     

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

            // Handle image uploads
            

            return redirect()->route('vendor.branches.index')
                ->with('success', __('dashboard.Branch created successfully'));

        // } catch (\Exception $e) {
        //     Log::error('Failed to create branch', ['error' => $e->getMessage()]);
        //     return back()->withInput()
        //         ->with('error', __('Failed to create branch: ') . $e->getMessage());
        // }
    }

    public function edit(Branch $branch)
    {
        return Inertia::render('Vendor/Branches/Edit', [
            'branch' => $branch,
            'cities' => City::select(['id as value', 'name as label'])->limit(10)->get()->toArray(),
        ]);
    }

    public function update(BranchRequest $request, Branch $branch)
    {

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
                    Storage::disk('public')->delete($image->path);
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
                ->with('success', __('dashboard.Branch updated successfully'));

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', __('dashboard.Failed to update branch: ') . $e->getMessage());
        }
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return back()->with('success', __('dashboard.Branch deleted successfully'));
    }

    public function toggleStatus(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);

        return back()->with('success', __('dashboard.Branch status updated successfully'));
    }
}