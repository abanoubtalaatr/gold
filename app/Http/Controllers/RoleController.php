<?php

namespace  App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read roles', ['only' => ['index']]);
        $this->middleware('permission:create roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:update roles', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete roles', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $roles = Role::latest()->with('translations');

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
    \Log::info('Store Request data:', $request->all());

    DB::beginTransaction();
    try {
        $role = Role::create([
            'key' => $request->input('translations.en.name'),
            'name' => $request->input('translations.en.name'),
            'guard_name' => 'web',
        ]);

        foreach ($request->input('translations') as $locale => $translation) {
            DB::table('role_translations')
                ->insert([
                    'role_id' => $role->id,
                    'locale' => $locale,
                    'name' => $translation['name']
                ]);
        }

        DB::commit();


        return redirect()->route('roles.index')
            ->with('success', __('messages.data_created_successfully'));

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Store failed:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Error creating role: ' . $e->getMessage());
    }
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
   $role->load('translations');


    return Inertia('roles-permissions/Roles/Edit', [
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
        \Log::info('Request data:', $request->all());

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


            return redirect()->route('roles.index')
                ->with('success', __('messages.data_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    public function destroy($roleId)
    {
        $role = Role::find($roleId);
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