<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:read users', ['only' => ['index']]);
        $this->middleware('permission:create users', ['only' => ['create']]);
        $this->middleware('permission:update users', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete users', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // Define the filters
        $filters = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ];

        $vendorId = Auth::user()->vendor_id ?? Auth::user()->id;
        $UsersQuery = User::with('roles')->where('vendor_id', $vendorId)->latest();


        $UsersQuery->when($filters['name'], function ($query, $name) {
            return $query->where('name', 'LIKE', "%{$name}%");
        });

        $UsersQuery->when($filters['email'], function ($query, $email) {
            return $query->where('email', 'LIKE', "%{$email}%");
        });


        if (isset($filters['is_active'])) {
            $UsersQuery->where('is_active', $filters['is_active']);
        }
        // Paginate the filtered users
        $users = $UsersQuery->paginate(10);

        return Inertia('Users/index', [
            'filters' => $filters,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendorId = Auth::user()->vendor_id ?? Auth::user()->id;
        
        // Get roles with name and name_ar
        $roles = Role::where('vendor_id', $vendorId)
            ->select('id', 'name', 'name_ar')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'name_ar' => $role->name_ar,
                    'display_name' => app()->isLocale('ar') ? $role->name_ar : $role->name
                ];
            });
        
        return Inertia('Users/Create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'avatar' => 'avatars/default_avatar.png', // Default value
            'vendor_id' => Auth::user()->id
        ];

        if ($request->hasFile('avatar')) {
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($userData);

        // Sync roles if provided
        if ($request->has('selectedRoles') && is_array($request->selectedRoles)) {
            $user->syncRoles($request->selectedRoles);
        }

        return redirect()->route('users.index')
            ->with('success', __('messages.data_saved_successfully'));
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $vendorId = Auth::user()->vendor_id ?? Auth::user()->id;
        
        // Get roles with name and name_ar
        $roles = Role::where('vendor_id', $vendorId)
            ->select('id', 'name', 'name_ar')
            ->get()
            ->filter(function ($role) {
                // Filter out superadmin roles
                return $role->name !== 'superadmin' && !empty($role->name);
            })
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'name_ar' => $role->name_ar,
                    'display_name' => app()->isLocale('ar') ? $role->name_ar : $role->name
                ];
            })
            ->values()
            ->toArray();

        $userRoles = $user->roles->pluck('name')->all();

        return Inertia('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar && $user->avatar !== 'avatars/default_avatar.png') {
                    Storage::disk('public')->delete($user->avatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $path;

                Log::info('Image uploaded:', ['path' => $path]);
            }

            Log::info('Updating user with data:', $userData);

            // Update user data
            $user->update($userData);

            // Update roles if provided
            if ($request->has('selectedRoles') && is_array($request->selectedRoles)) {
                $user->syncRoles($request->selectedRoles);
            }

            DB::commit();

            return redirect()
                ->route('users.index')
                ->with('success', __('messages.data_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User Update Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', $e->getMessage());
        }
    }


    public function activate(User $user)
    {
        $user->update(
            [
                'is_active' => ($user->is_active) ? 0 : 1
            ]
        );
        
        return redirect()->route('users.index')
            ->with('success', __('messages.status_updated'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Remove avatar file if exists
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        // Remove logo file if exists
        if ($user->logo) {
            Storage::delete($user->logo);
        }

        // Remove commercial registration image if exists
        if ($user->commercial_registration_image) {
            Storage::delete($user->commercial_registration_image);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('messages.data_deleted_successfully'));
    }
}
