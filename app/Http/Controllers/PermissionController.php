<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

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
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create([
            'name' => $request->name,
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
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$id,
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
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
