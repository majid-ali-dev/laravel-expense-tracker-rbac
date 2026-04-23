<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(5);

        return view('manager.role_permissions.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get();

        return view('manager.role_permissions.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ], [
            'permissions.array' => 'Permissions selection is invalid.',
            'permissions.*.integer' => 'Each selected permission must be valid.',
            'permissions.*.exists' => 'One or more selected permissions are invalid.',
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('role.permissions.index')
            ->with('success', 'Permissions Assigned Successfully');
    }
}
