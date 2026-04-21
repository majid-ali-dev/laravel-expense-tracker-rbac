<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

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
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
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
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
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
