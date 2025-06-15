<?php

namespace  App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct() {}

    public function index(Request $request)
    {
        $roles = Role::where('vendor_id', Auth::user()->id)->latest();

        $filters = [
            'name' => $request->name,
            'is_active' => $request->is_active,
        ];

        $roles->when($filters['name'], function ($roles, $name) {
            return $roles->where('name', "%{$name}%")->orWhere('name_ar', "%{$name}%");
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

        return Inertia('roles-permissions/Roles/index', [
            'roles' => $roles,
        ]);
    }



    public function create()
    {
        return Inertia('roles-permissions/Roles/Create');
    }

    public function store(StoreRoleRequest $request)
    {
        $vendorId = Auth::user()->vendor_id ?? Auth::user()->id;
        
        // Check if role already exists for this vendor
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
        
        return redirect()->route('roles.index')
            ->with('success', __('messages.data_created_successfully'));
    }


    // public function edit(Role $role)
    // {
    //     return
    //     Inertia('roles-permissions/Roles/Edit', [
    //         'role' => $role
    //     ]);
    // }

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

        return redirect()->route('roles.index')
            ->with('success', __('messages.data_updated_successfully'));
    }

    public function destroy($roleId)
    {

        $role = Role::find($roleId->id);
        $role->delete();
        return redirect()->route('roles.index')
            ->with('success',  __('messages.data_deleted_successfully'));
    }

    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role->id)
            ->pluck('permissions.name')
            ->all();


        return Inertia('roles-permissions/Roles/Add-permissions', [
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
        $parentRole = User::find(Auth::user()->vendor_id ?? Auth::user()->id);

        if (Auth::user()->hasRole('vendor') || $parentRole->hasRole('vendor')) {
            return redirect()->route('vendor.roles.index')
                ->with('success',  __('messages.role_permissions_updated_successfully'));
        }
        return redirect()->route('roles.index')
            ->with('success',  __('messages.role_permissions_updated_successfully'));
    }


    public function activate(Role $role)
    {
        $role->update(
            [
                'is_active' => ($role->is_active) ? 0 : 1
            ]
        );
        return redirect()->route('roles.index')
            ->with('success', __('messages.status_updated'));
    }
}
