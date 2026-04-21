<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    // Show all roles
    public function index()
    {
        $roles = Role::paginate(5);

        return view('manager.role_and_permissions.index', compact('roles'));
    }

    // Show permissions assign page
    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('manager.role_and_permissions.edit', compact('role', 'permissions'));
    }

    // Update permissions for role
    public function update(Request $request, Role $role)
    {
        // sync permissions
        $role->permissions()->sync($request->permissions);

        return redirect()->route('role.permissions.index')
            ->with('success', 'Permissions Assigned Successfully');
    }
}
