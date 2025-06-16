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
    public function index(Request $request)
    {
        // Get the current authenticated user (assuming it's a vendor)
        $user = Auth::user();

        $roles = Role::where('name', '!=', 'superadmin')->where('vendor_id', $user->vendor_id ?? $user->id)
            ->latest();

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
                'name' => app()->getLocale() == 'ar' ? $role->name_ar : $role->name,
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
        $vendorId = Auth::user()->vendor_id ?? Auth::user()->id;
        
        $existingRole = Role::where('name', $request->input('translations.en.name'))
                           ->where('guard_name', 'web')
                           ->where('vendor_id', $vendorId)
                           ->first();
        
        if ($existingRole) {
            return redirect()->back()
                ->withErrors(['translations.en.name' => 'A role with this name already exists for your account.'])
                ->withInput();
        }

        $role = Role::create([
            'key' => $request->input('translations.en.name'),
            'name' => $request->input('translations.en.name'),
            'name_ar' => $request->input('translations.ar.name'),
            'guard_name' => 'web',
            'vendor_id' => $vendorId
        ]);
        

        return redirect()->route('vendor.roles.index')
            ->with('success', __('messages.data_created_successfully'));
    }
    public function edit(Role $role)
    {
        return Inertia('roles-permissions/Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' =>  $role->name,
                'name_ar' =>  $role->name_ar,
                'guard_name' => $role->guard_name,
            ],
        ]);
    }


    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->input('name'),
            'name_ar' => $request->input('name_ar'),
        ]);

        return redirect()->route('vendor.roles.index')
            ->with('success', __('messages.data_updated_successfully'));
    }

    public function destroy($role)
    {
        $role->delete();
        
        return redirect()->route('vendor.roles.index')
            ->with('success', __('messages.data_deleted_successfully'));
    }

    public function addPermissionToRole($roleId)
    {
        // Get the role we want to add permissions to
        $role = Role::findOrFail($roleId);

        // Get all vendor-prefixed permissions
        $permissions = Permission::where('name', 'like', 'vendor %')->get();;
        // Get permissions already assigned to this role
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
        $request->validate([
            'selectedPermissions' => 'required'
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->selectedPermissions);
        return redirect()->route('vendor.roles.index')
            ->with('success', __('messages.role_permissions_updated_successfully'));
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
