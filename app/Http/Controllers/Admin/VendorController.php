<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\User;
use App\Notifications\Vendor\VendorApprovedNotification;
use App\Notifications\Vendor\VendorRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class VendorController extends Controller
{
    // List all vendors
    public function index()
    {
        $vendors = User::role('vendor')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Vendors/Index', [
            'vendors' => $vendors,
        ]);
    }

    // Show create form
    public function create()
    {
        $cities = City::select('id', 'name')->limit(value: 10)->get();
        return Inertia::render('Admin/Vendors/Create', [
            'cities' => $cities,
        ]);
    }

    // Store new vendor
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|max:20|unique:users',
            'dialling_code' => 'required|string|max:5',
            'password' => 'required|string|min:8|confirmed',
            'store_name_en' => 'required|string|max:255',
            'store_name_ar' => 'required|string|max:255',
            'commercial_registration_number' => 'required|string|max:255',
            'commercial_registration_image' => 'required|image|max:2048',
            'logo' => 'required|image|max:2048',
            'iban' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'accept_terms' => 'required|accepted',
        ]);

        // Store images manually
        $commercialRegistrationPath = null;
        if ($request->hasFile('commercial_registration_image')) {
            $file = $request->file('commercial_registration_image');
            $commercialRegistrationPath = $file->store('uploads/commercial_registrations', 'public');
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $logoPath = $file->store('uploads/logos', 'public');
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'dialling_code' => $validated['dialling_code'],
            'password' => bcrypt($validated['password']),
            'store_name_en' => $validated['store_name_en'],
            'store_name_ar' => $validated['store_name_ar'],
            'commercial_registration_number' => $validated['commercial_registration_number'],
            'commercial_registration_image_path' => $commercialRegistrationPath,
            'logo_path' => $logoPath,
            'iban' => $validated['iban'],
            'city_id' => $validated['city_id'],
            'accept_terms' => true,
            'vendor_status' => 'approved', // Auto-approve admin-created vendors
            'is_active' => true,
        ]);

        // Assign vendor role
        $user->assignRole('vendor');

        return redirect()->route('vendors.index')
            ->with('success', __('Vendor created successfully'));
    }

    // Show vendor details
    public function show(User $vendor)
    {
        // Eager load the city and branches relationships
        $vendor->load([
            'city' => function ($query) {
                $query->select('id', 'name'); // Only select needed columns
            },
            'branches' => function ($query) {
                $query->select('id', 'vendor_id', 'name', 'city_id', 'address', 'is_active', 'working_days', 'working_hours', 'logo')
                    ->with('city:id,name');
            },
        ]);

        
        return Inertia::render('Admin/Vendors/Show', [
            'vendor' => $vendor->append([
                'commercial_registration_image_url',
                'logo_url',
                'store_name'
            ]),
            'branches' => $vendor->branches,
        ]);
    }

    // Approve vendor
    public function approve(User $vendor)
    {

        $vendor->update([
            'vendor_status' => 'approved',
            'rejection_reason' => null,
        ]);

        // Send approval notification
        $vendor->notify(new VendorApprovedNotification());

        return back()->with('success', __('Vendor approved successfully'));
    }

    // Reject vendor
    public function reject(Request $request, User $vendor)
    {

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $vendor->update([
            'vendor_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Send rejection notification
        $vendor->notify(new VendorRejectedNotification($validated['rejection_reason']));

        return back()->with('success', __('Vendor rejected successfully'));
    }

    // Toggle vendor active status
    public function toggleStatus(User $vendor)
    {

        $vendor->update([
            'is_active' => !$vendor->is_active,
        ]);

        $status = $vendor->is_active ? 'activated' : 'deactivated';

        return back()->with('success', __("Vendor {$status} successfully"));
    }

    // Show edit form
    public function edit(User $vendor)
    {

        $vendor->load(['city']);
        $cities = City::select('id', 'name')->limit(value: 10)->get();


        return Inertia::render('Admin/Vendors/Edit', [
            'vendor' => $vendor,
            'cities' => $cities,
        ]);
    }

    // Update vendor
    public function update(Request $request, User $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($vendor->id)],
            'mobile' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($vendor->id)],
            'dialling_code' => 'required|string|max:5',
            'store_name_en' => 'required|string|max:255',
            'store_name_ar' => 'required|string|max:255',
            'commercial_registration_number' => 'required|string|max:255',
            'commercial_registration_image' => 'nullable|image|max:2048',
            'logo' => 'nullable|image|max:2048',
            'iban' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        $updateData = $validated;

        // Handle commercial registration image
        if ($request->hasFile('commercial_registration_image')) {
            if ($vendor->commercial_registration_image) {
                Storage::disk('public')->delete($vendor->commercial_registration_image);
            }
            $file = $request->file('commercial_registration_image');
            $path = $file->store('uploads/commercial_registrations', 'public');
            $updateData['commercial_registration_image'] = $path;
        } else {
            // Remove the key if exists to prevent overwriting
            unset($updateData['commercial_registration_image']);
        }

        // Handle logo
        if ($request->hasFile('logo')) {
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $file = $request->file('logo');
            $path = $file->store('uploads/logos', 'public');
            $updateData['logo'] = $path;
        } else {
            unset($updateData['logo']);
        }

        // Final update
        $vendor->update($updateData);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', __('Vendor updated successfully'));
    }
}