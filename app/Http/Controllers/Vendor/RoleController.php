<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:vendor read roles', ['only' => ['index']]);
        $this->middleware('permission:vendor create roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:vendor update roles', ['only' => ['update', 'edit']]);
        $this->middleware('permission:vendor delete roles', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // Get the current authenticated user (assuming it's a vendor)
        $user = Auth::user();

        $roles = Role::where('name', '!=', 'superadmin')
            ->latest()
            ->with('translations');

        // If the user is a vendor, only show roles associated with this vendor
        if ($user && $user->hasRole('vendor')) {
            $roles->where(function ($query) use ($user) {
                $query->where('vendor_id', $user->id);
            });
        }

        $filters = [
            'name' => $request->name,
            'is_active' => $request->is_active,
        ];

        $roles->when($filters['name'], function ($roles, $name) {
            return $roles->whereTranslationLike('name', "%{$name}%");
        });

        if (isset($filters['is_active'])) {
            $roles->where('is_active', $filters['is_active']);
        }

        $roles = $roles->paginate(10);
        $roles->getCollection()->transform(function ($role) {
            return [
                'id' => $role->id,
                'is_active' => $role->is_active,
                'name' => $role->translate(app()->getLocale())?->name ?? $role->name,
                'guard_name' => $role->guard_name,
            ];
        });

        return Inertia('Vendor/roles-permissions/Roles/index', [
            'roles' => $roles,
        ]);
    }



    public function create()
    {
        return Inertia('Vendor/roles-permissions/Roles/Create');
    }


    public function store(StoreRoleRequest $request)
    {
        DB::beginTransaction();
        try {
            // First create the role
            $roleId = DB::table('roles')->insertGetId([
                'name' => $request->input('translations.en.name'),
                'guard_name' => 'web',
                'vendor_id' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Then add translations
            foreach ($request->input('translations') as $locale => $translation) {
                DB::table('role_translations')->insert([
                    'role_id' => $roleId,
                    'locale' => $locale,
                    'name' => $translation['name']
                ]);
            }

            DB::commit();

            return redirect()->route('vendor.roles.index')
                ->with('success', __('messages.data_created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role creation failed: ' . $e->getMessage());
            return back()->with('error', 'Error creating role');
        }
    }
    public function edit(Role $role)
    {
        $role->load('translations');


        return Inertia('Vendor/roles-permissions/Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' =>  $role->name,
                'guard_name' => $role->guard_name,
                'translations' => $role->translations,
            ],
        ]);
    }


    public function update(UpdateRoleRequest $request, Role $role)
    {
        DB::beginTransaction();
        try {

            DB::table('roles')
                ->where('id', $role->id)
                ->update(['name' => $request->input('translations.en.name')]);

            foreach ($request->input('translations') as $locale => $translation) {
                DB::table('role_translations')
                    ->updateOrInsert(
                        [
                            'role_id' => $role->id,
                            'locale' => $locale
                        ],
                        [
                            'name' => $translation['name']
                        ]
                    );
            }

            DB::commit();


            return redirect()->route('vendor.roles.index')
                ->with('success', __('messages.data_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    public function destroy($roleId)
    {
        $role = Role::find($roleId)->first();
        $role->delete();
        return redirect()->route('vendor.roles.index')
            ->with('success',  __('messages.data_deleted_successfully'));
    }

    public function addPermissionToRole($roleId)
    {
        // i want to pluck the ids of permissions that are already assigned to the role
        $role = Role::where('name', 'vendor')->first();
        
        $permissions = Permission::whereIn('id', $role->permissions->pluck('id'))->get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role->id)
            ->pluck('permissions.name')
            ->all();


        return Inertia('Vendor/roles-permissions/Roles/Add-permissions', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function givePermissionToRole(Request $request, $roleId)
    {
        dd($roleId);
        $request->validate([
            'selectedPermissions' => 'required'
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->selectedPermissions);
        return redirect()->route('vendor.roles.index')
            ->with('success',  __('messages.role_permissions_updated_successfully'));
    }


    public function activate(Role $role)
    {
        $role->update(
            [
                'is_active' => ($role->is_active) ? 0 : 1
            ]
        );
        return redirect()->route('vendor.roles.index')
            ->with('success', __('messages.status_updated'));
    }
}
