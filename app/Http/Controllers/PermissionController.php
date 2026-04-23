<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    // Show all permissions
    public function index()
    {
        $permissions = Permission::latest()->paginate(5);

        return view('manager.permissions.index', compact('permissions'));
    }

    // Show create form
    public function create()
    {
        return view('manager.permissions.create');
    }

    // Store new permission
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ], [
            'name.required' => 'Permission name field is required.',
            'name.string' => 'Permission name must be a valid text value.',
            'name.max' => 'Permission name may not be greater than 255 characters.',
            'name.unique' => 'This permission already exists.',
        ]);

        Permission::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission Created');
    }

    // Show edit form
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('manager.permissions.edit', compact('permission'));
    }

    // Update permission
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($permission->id)],
        ], [
            'name.required' => 'Permission name field is required.',
            'name.string' => 'Permission name must be a valid text value.',
            'name.max' => 'Permission name may not be greater than 255 characters.',
            'name.unique' => 'This permission already exists.',
        ]);
        $permission->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission Updated');
    }

    // Delete permission
    public function destroy($id)
    {
        Permission::findOrFail($id)->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission Deleted');
    }
}
