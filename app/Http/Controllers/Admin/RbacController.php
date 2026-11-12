<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Module;
use App\Models\Role;
use App\Models\RoleModulePermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RbacController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::where('name', '!=', 'super_admin')->get();
        $modules = Module::with(['submodules'])->orderBy('order')->get();

        // Load existing permissions indexed by [role_id][module_id][submodule_id]
        $rawPerms = RoleModulePermission::all();
        $permissionsMatrix = [];

        foreach ($rawPerms as $p) {
            $subKey = $p->submodule_id ?? 0;
            $permissionsMatrix[$p->role_id][$p->module_id][$subKey] = [
                'can_view' => $p->can_view,
                'can_create' => $p->can_create,
                'can_edit' => $p->can_edit,
                'can_delete' => $p->can_delete,
            ];
        }

        $selectedRole = $request->has('role_id')
            ? Role::find($request->role_id)
            : $roles->first();

        return view('admin.rbac.index', compact('roles', 'modules', 'permissionsMatrix', 'selectedRole'));
    }

    public function updateMatrix(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
        ]);

        $roleId = $validated['role_id'];
        $role = Role::findOrFail($roleId);

        if ($role->name === 'super_admin') {
            return back()->with('error', 'Super Admin inherently possesses complete access across all modules.');
        }

        $submittedPermissions = $request->input('permissions', []);

        $modules = Module::with('submodules')->get();

        foreach ($modules as $mod) {
            // Module-level permission
            $modData = $submittedPermissions[$mod->id][0] ?? [];
            RoleModulePermission::updateOrCreate(
                [
                    'role_id' => $roleId,
                    'module_id' => $mod->id,
                    'submodule_id' => null,
                ],
                [
                    'can_view' => isset($modData['can_view']),
                    'can_create' => isset($modData['can_create']),
                    'can_edit' => isset($modData['can_edit']),
                    'can_delete' => isset($modData['can_delete']),
                ]
            );

            // Submodules
            foreach ($mod->submodules as $sub) {
                $subData = $submittedPermissions[$mod->id][$sub->id] ?? [];
                RoleModulePermission::updateOrCreate(
                    [
                        'role_id' => $roleId,
                        'module_id' => $mod->id,
                        'submodule_id' => $sub->id,
                    ],
                    [
                        'can_view' => isset($subData['can_view']),
                        'can_create' => isset($subData['can_create']),
                        'can_edit' => isset($subData['can_edit']),
                        'can_delete' => isset($subData['can_delete']),
                    ]
                );
            }
        }

        ActivityLog::log('rbac_matrix_updated', "Updated module permissions for role [{$role->display_name}]");

        return back()->with('success', "Dynamic permissions for role [{$role->display_name}] updated successfully.");
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $name = Str::slug($validated['display_name'], '_');

        if (Role::where('name', $name)->exists()) {
            return back()->with('error', 'A role with this identifier already exists.');
        }

        $role = Role::create([
            'name' => $name,
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        ActivityLog::log('role_created', "Created new custom role [{$role->display_name}]");

        return back()->with('success', "Role [{$role->display_name}] created successfully.");
    }

    public function auditLogs(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', 'like', "%{$request->action}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.rbac.logs', compact('logs'));
    }
}
