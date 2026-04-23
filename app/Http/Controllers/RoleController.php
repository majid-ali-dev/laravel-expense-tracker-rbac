<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Show all roles
    public function index()
    {
        $roles = Role::latest()->paginate(5);

        return view('manager.roles.index', compact('roles'));
    }

    // Show create form
    public function create()
    {
        return view('manager.roles.create');
    }

    // Store new role
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ], [
            'name.required' => 'Role name field is required.',
            'name.string' => 'Role name must be a valid text value.',
            'name.max' => 'Role name may not be greater than 255 characters.',
            'name.unique' => 'This role already exists.',
        ]);

        Role::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('roles.index')->with('success', 'Role Created');
    }

    // Show edit form
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        return view('manager.roles.edit', compact('role'));
    }

    // Update role
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
        ], [
            'name.required' => 'Role name field is required.',
            'name.string' => 'Role name must be a valid text value.',
            'name.max' => 'Role name may not be greater than 255 characters.',
            'name.unique' => 'This role already exists.',
        ]);
        $role->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('roles.index')->with('success', 'Role Updated');
    }

    // Delete role
    public function destroy($id)
    {
        Role::findOrFail($id)->delete();

        return redirect()->route('roles.index')->with('success', 'Role Deleted');
    }
}
