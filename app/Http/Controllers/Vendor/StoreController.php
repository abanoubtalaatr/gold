<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdateStoreRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\User;
use App\Notifications\Admin\VendorResubmissionNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function show()
    {
        $vendor = Auth::user();

        if (!$vendor instanceof \App\Models\User) {
            return redirect()->back()->with('error', 'User not found');
        }
        $vendor->load(['storeAddress', 'storeAddress.city']);

        // Convert closed values from string to boolean
        $workingHours = $vendor->working_hours
            ? json_decode($vendor->working_hours, true)
            : null;

        if ($workingHours) {
            $workingHours = array_map(function ($day) {
                $day['closed'] = (bool) $day['closed']; // Convert string "1" to boolean true
                return $day;
            }, $workingHours);
        }

        return Inertia::render('Vendor/Store/Show', [
            'store' => [
                'name' => $vendor->name,
                'email' => $vendor->email,
                'mobile' => $vendor->mobile,
                'dialling_code' => $vendor->dialling_code,
                'store_name_en' => $vendor->store_name_en,
                'store_name_ar' => $vendor->store_name_ar,
                'commercial_number' => $vendor->commercial_registration_number,
                'commercial_image' => $vendor->commercial_registration_image
                    ? asset("storage/{$vendor->commercial_registration_image}")
                    : null,
                'logo' => $vendor->logo
                    ? asset("storage/{$vendor->logo}")
                    : null,
                'iban' => $vendor->iban,
                'address' => $vendor->storeAddress ? [
                    'address' => $vendor->storeAddress->address,
                    'city_id' => $vendor->storeAddress->city_id,
                    'city' => $vendor->storeAddress->city->name
                ] : null,
                'working_hours' => $workingHours,
                'status' => $vendor->vendor_status, // Fixed typo (venodr_status -> vendor_status)
                'rejection_reason' => $vendor->rejection_reason
            ]
        ]);
    }

    public function edit()
    {
        $vendor = Auth::user();
        if (!$vendor instanceof \App\Models\User) {
            return redirect()->back()->with('error', 'User not found');
        }
        $vendor->load(['storeAddress', 'storeAddress.city']);

        // Get only the first 100 cities ordered by name
        $cities = City::orderBy('name')->limit(400)->get(['id', 'name']);

        return Inertia::render('Vendor/Store/Edit', [
            'store' => [
                'name' => $vendor->name,
                'email' => $vendor->email,
                'mobile' => $vendor->mobile,
                'dialling_code' => $vendor->dialling_code,
                'store_name_en' => $vendor->store_name_en,
                'store_name_ar' => $vendor->store_name_ar,
                'commercial_number' => $vendor->commercial_registration_number,
                'commercial_image' => $vendor->commercial_registration_image
                    ? asset("storage/{$vendor->commercial_registration_image}")
                    : null,
                'logo' => $vendor->logo
                    ? asset("storage/{$vendor->logo}")
                    : null,
                'iban' => $vendor->iban,
                'address' => $vendor->storeAddress ? [
                    'address' => $vendor->storeAddress->address,
                    'city_id' => $vendor->storeAddress->city_id,
                    'city' => $vendor->storeAddress->city->name
                ] : ['address' => '', 'city_id' => null],
                'working_hours' => json_decode($vendor->working_hours, true),
                'cities' => $cities
            ]
        ]);
    }

   public function update(UpdateStoreRequest $request)
{
    $vendor = Auth::user();

    if (!$vendor instanceof \App\Models\User) {
        return redirect()->back()->with('error', 'User not found');
    }

    $data = $request->validated();

    // Handle commercial image update only if provided
    if ($request->hasFile('commercial_image')) {
        if ($vendor->commercial_registration_image) {
            Storage::delete($vendor->commercial_registration_image);
        }
        $data['commercial_registration_image'] = $request->file('commercial_image')
            ->store('commercial_registrations', 'public');
    } else {
        // Remove commercial_image from data if not provided to prevent nulling
        unset($data['commercial_image']);
    }

    // Handle logo update only if provided
    if ($request->hasFile('logo')) {
        if ($vendor->logo) {
            Storage::delete($vendor->logo);
        }
        $data['logo'] = $request->file('logo')
            ->store('vendor_logos', 'public');
    } else {
        // Remove logo from data if not provided to prevent nulling
        unset($data['logo']);
    }

    // Handle working hours
    if ($request->has('working_hours')) {
        $data['working_hours'] = json_encode($request->working_hours);
    }

    // Handle address
    if ($request->has('address')) {
        $vendor->addresses()->updateOrCreate(
            ['is_default' => true],
            [
                'address' => $request->address['address'],
                'city_id' => $request->address['city_id'],
                'is_default' => true,
            ]
        );
    }

    // Handle vendor status change if commercial number changed
    if (
        $request->has('commercial_number') &&
        $request->commercial_number !== $vendor->commercial_registration_number
    ) {
        $data['vendor_status'] = 'pending';
    }

    // Update only the fields that are present in the data array
    $vendor->update($data);

    return redirect()->route('vendor.store.show')
        ->with('success', 'Store information updated successfully');
}

    public function resubmit()
    {
        $vendor = Auth::user();

        if (!$vendor instanceof \App\Models\User) {
            return redirect()->back()->with('error', 'User not found');
        }

        // Update vendor status to pending and clear rejection reason
        $vendor->update([
            'vendor_status' => 'pending',
            'rejection_reason' => null
        ]);

        // Get all admin users to notify them
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin')
                ->orWhere('name', 'superadmin')
                ->whereNull('vendor_id');
        })->get();

        // Send notification to all admins
        foreach ($admins as $admin) {
            $admin->notify(new VendorResubmissionNotification($vendor));
        }

        return redirect()->route('vendor.store.show')
            ->with('success', __('Application resubmitted for approval. The admin has been notified.'));
    }
}