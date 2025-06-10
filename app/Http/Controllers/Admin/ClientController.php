<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ClientController extends Controller
{
    // List all clients
    public function index(Request $request)
    {
        $query = User::doesntHave('roles')
            ->with('city:id,name');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('iban', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
            // If status is 'all', don't add any filter
        }

        $clients = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    // Show create form
    public function create()
    {
        $cities = City::select('id', 'name')->limit(10)->get();
        return Inertia::render('Admin/Clients/Create', [
            'cities' => $cities,
        ]);
    }

    // Store new client
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|string|max:20|unique:users',
            'dialling_code' => 'required|string|max:5',
            'password' => 'required|string|min:8|confirmed',
            'iban' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'dialling_code' => $validated['dialling_code'],
            'password' => bcrypt($validated['password']),
            'iban' => $validated['iban'],
            'city_id' => $validated['city_id'],
            'is_active' => true,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully');
    }

    // Show client details
    public function show(User $client)
    {
        $client->load('city:id,name');
        
        return Inertia::render('Admin/Clients/Show', [
            'client' => $client,
        ]);
    }

    // Toggle client active status
    public function toggleStatus(User $client)
    {
        $client->update([
            'is_active' => !$client->is_active,
        ]);

        $status = $client->is_active ? 'activated' : 'deactivated';

        return back()->with('success', __("Client {$status} successfully"));
    }

    // Show edit form
    public function edit(User $client)
    {
        $client->load('city:id,name');
        $cities = City::select('id', 'name')->limit(10)->get();

        return Inertia::render('Admin/Clients/Edit', [
            'client' => $client,
            'cities' => $cities,
        ]);
    }

    // Update client
    public function update(Request $request, User $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($client->id)],
            'mobile' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($client->id)],
            'dialling_code' => 'required|string|max:5',
            'iban' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client updated successfully');
    }
}