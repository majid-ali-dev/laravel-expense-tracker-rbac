<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // hide cuurect user logend and only fecth other users where role is member
        $users = User::with('roles')
            ->where('id', '!=', auth()->id())
            ->whereHas('roles', function ($q) {
                $q->where('name', 'member');
            })
            ->paginate(5);

        return view('manager.users.index', compact('users'))->with('success', 'Users List');
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('manager.users.create', compact('roles'))->with('success', 'User creation page');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:5'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ], [
            'name.required' => 'Name field is required.',
            'name.string' => 'Name must be a valid text value.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'Phone field is required.',
            'phone.string' => 'Phone must be a valid text value.',
            'phone.max' => 'Phone may not be greater than 255 characters.',
            'password.required' => 'Password field is required.',
            'password.string' => 'Password must be a valid text value.',
            'password.min' => 'Password must be at least 5 characters.',
            'roles.array' => 'Roles selection is invalid.',
            'roles.*.integer' => 'Each selected role must be valid.',
            'roles.*.exists' => 'One or more selected roles are invalid.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
        ]);

        $roleIds = $validated['roles'] ?? [];

        if (empty($roleIds)) {
            $memberRoleId = Role::where('name', 'member')->value('id');

            if ($memberRoleId) {
                $roleIds = [$memberRoleId];
            }
        }

        $user->roles()->sync($roleIds);

        return redirect()->route('users.index')->with('success', 'User Create Successful');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::orderBy('name')->get();

        return view('manager.users.edit', compact('user', 'roles'))->with('success', 'User Edit Page');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:255'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ], [
            'name.required' => 'Name field is required.',
            'name.string' => 'Name must be a valid text value.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'Phone field is required.',
            'phone.string' => 'Phone must be a valid text value.',
            'phone.max' => 'Phone may not be greater than 255 characters.',
            'roles.array' => 'Roles selection is invalid.',
            'roles.*.integer' => 'Each selected role must be valid.',
            'roles.*.exists' => 'One or more selected roles are invalid.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $user->roles()->sync($validated['roles'] ?? []);

        return redirect()->route('users.index')->with('success', 'User Update Successful');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User Delete Successful');
    }
}
