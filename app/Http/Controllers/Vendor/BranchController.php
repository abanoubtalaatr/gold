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
        
        $branches = $this->branchService->list($request->user()->vendor_id??$request->user()->id, $filters);

        return Inertia::render('Vendor/Branches/Index', [
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function create()
    {        
        
        $country = Country::find(194);

        // i want to get all cities for this all states for this country  
        $states = State::where('country_id', $country->id)->pluck('id')->toArray();
        
        $cities = City::whereIn('state_id',$states)->where('status',1)->select(['id as value', 'name as label'])->get()->toArray();

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

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('branches/logos', 'public');
            }

            // Create branch
            $branch = Branch::create([
                'vendor_id' => $request->user()->id,
                'name' => $validated['name'],
                'city_id' => $validated['city_id'],
                'working_days' => $validated['working_days'],
                'working_hours' => $validated['working_hours'],
                'logo' => $logoPath,
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
        // Load the branch with city relationship
        $branch->load('city');
        $country = Country::find(194);

        // i want to get all cities for this all states for this country  
        $states = State::where('country_id', $country->id)->pluck('id')->toArray();
        
        $cities = City::whereIn('state_id',$states)->where('status',1)->select(['id as value', 'name as label'])->get()->toArray();

        return Inertia::render('Vendor/Branches/Edit', [
            'branch' => $branch,
            'cities' =>$cities,
        ]);
    }

    public function update(BranchRequest $request, Branch $branch)
    {

        try {
            $validated = $request->validated();
            $validated['working_days'] = array_map('intval', $validated['working_days']);

            // Handle logo upload/update/removal
            $updateData = [
                'name' => $validated['name'],
                'city_id' => $validated['city_id'],
                'working_days' => $validated['working_days'],
                'working_hours' => $validated['working_hours'],
            ];

            // Check if a new logo is uploaded
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($branch->logo && Storage::disk('public')->exists($branch->logo)) {
                    Storage::disk('public')->delete($branch->logo);
                }
                
                // Store new logo
                $updateData['logo'] = $request->file('logo')->store('branches/logos', 'public');
            }
            // Check if logo should be removed
            elseif ($request->input('removeLogo') === true || $request->input('removeLogo') === 'true') {
                // Delete existing logo
                if ($branch->logo && Storage::disk('public')->exists($branch->logo)) {
                    Storage::disk('public')->delete($branch->logo);
                }
                $updateData['logo'] = null;
            }

            $branch->update($updateData);

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
        // Delete logo file if exists
        if ($branch->logo && Storage::disk('public')->exists($branch->logo)) {
            Storage::disk('public')->delete($branch->logo);
        }

        // Delete images if any exist
        if ($branch->images) {
            foreach ($branch->images as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }
        }

        $branch->delete();

        return back()->with('success', __('dashboard.Branch deleted successfully'));
    }

    public function toggleStatus(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);

        return back()->with('success', __('dashboard.Branch status updated successfully'));
    }
}