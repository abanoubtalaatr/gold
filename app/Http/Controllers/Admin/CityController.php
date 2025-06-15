<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);

        $cities = City::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('name_ar', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status === 'active');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Cities/Index', [
            'cities' => $cities,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        // $states = State::where('country_id', 194)->get();
        return Inertia::render('Admin/Cities/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        City::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'status' => $request->status === 'active',
            'state_id' => State::first()->id,
        ]);

        return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
    }
    public function show(City $city)
    {
        $city->load('state');
        return Inertia::render('Admin/Cities/Show', [
            'city' => $city,
        ]);
    }

    public function edit(City $city)
    {
        return Inertia::render('Admin/Cities/Edit');
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $city->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        // Check if city has users with orders
        $hasUsersWithOrders = $city->usersWithOrders()->exists();
        
        // Check if city has active vendors
        $hasActiveVendors = $city->activeVendors()->exists();

        if ($hasUsersWithOrders && $hasActiveVendors) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'city' => 'Cannot delete city. It has associated orders and active service providers.'
            ]);
        }

        if ($hasUsersWithOrders) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'city' => __('Cannot delete city. It has associated orders.')
            ]);
        }

        if ($hasActiveVendors) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'city' => __('Cannot delete city. It has active service providers.')
            ]);
        }

        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully.');
    }
    public function toggleStatus(City $city)
    {
        // Get the raw status value (bypassing any accessors)
        $currentStatus = $city->getRawOriginal('status');

        // Check if we're trying to deactivate (current status is true)
        // if ($currentStatus && ($city->usersWithOrders()->exists() || $city->activeVendors()->exists())) {
        //     return back()->with('error', 'Cannot deactivate city associated with active orders or service providers.');
        // }

        // Toggle the status
        $city->update(['status' => !$currentStatus]);

        return back()->with('success', 'City status updated successfully.');
    }
}