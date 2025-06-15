<?php

namespace App\Http\Controllers\Vendor;

use App\Models\City;
use App\Models\User;
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
    public function __construct(protected BranchService $branchService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search']);

        $branches = $this->branchService->list($request->user()->vendor_id ?? $request->user()->id, $filters);

        return Inertia::render('Vendor/Branches/Index', [
            'branches' => $branches,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request)
    {
        $cities = City::query()
            ->where('status', '=', 1)
            ->get()
            ->map(function ($city) {
                return [
                    'value' => $city->id,
                    'label' => app()->getLocale() === 'ar' ? $city->name_ar : $city->name
                ];
            });
        $users = User::where('vendor_id', $request->user()->vendor_id ?? $request->user()->id)->get();

        return Inertia::render('Vendor/Branches/Create', [
            'cities' => $cities,
            'users' => $users,
        ]);
    }

    public function store(BranchRequest $request)
    {
        
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
            'vendor_id' => $request->user()->vendor_id ?? $request->user()->id,
            'name' => $validated['name'],
            'city_id' => $validated['city_id'],
            'working_days' => $validated['working_days'],
            'working_hours' => $validated['working_hours'],
            'logo' => $logoPath,
            'address' => 'address',
            'is_active' => true,
            'user_id' => $validated['user_id'],
            'contact_number' => $validated['contact_number'],
            'contact_email' => $validated['contact_email'],
            'number_of_available_items' => $validated['number_of_available_items'] ?? 0,
        ]);

        return redirect()->route('vendor.branches.index')
            ->with('success', __('dashboard.Branch created successfully'));
    }

    public function edit(Branch $branch, Request $request)
    {
        // Load the branch with city relationship
        $branch->load('city');

        $cities = City::query()
        ->where('status', '=', 1)
        ->get()
        ->map(function ($city) {
            return [
                'value' => $city->id,
                'label' => app()->getLocale() === 'ar' ? $city->name_ar : $city->name
            ];
        });
        
        $users = User::where('vendor_id', $request->user()->vendor_id ?? $request->user()->id)->get();

        return Inertia::render('Vendor/Branches/Edit', [
            'branch' => $branch,
            'cities' => $cities,
            'users' => $users,
        ]);
    }

    public function update(BranchRequest $request, Branch $branch)
    {
            $validated = $request->validated();
            $validated['working_days'] = array_map('intval', $validated['working_days']);

            // Handle logo upload/update/removal
            $updateData = [
                'name' => $validated['name'],
                'city_id' => $validated['city_id'],
                'working_days' => $validated['working_days'],
                'working_hours' => $validated['working_hours'],
                'user_id' => $validated['user_id'],
                'contact_number' => $validated['contact_number'],
                'contact_email' => $validated['contact_email'],
                'number_of_available_items' => $validated['number_of_available_items'] ?? 0,
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

        // check if related to order sale or order rental
        if ($branch->orderRentals->count() > 0 || $branch->orderSales->count() > 0) {
            return back()->with('error', __('dashboard.Branch cannot be deleted because it has related orders'));
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
