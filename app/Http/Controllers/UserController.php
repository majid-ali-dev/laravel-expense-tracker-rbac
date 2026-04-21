<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->paginate(5);

        return view('manager.users.index', compact('users'))->with('success', 'Users List');
    }

    // Show create form
    public function create()
    {
        return view('manager.users.create')->with('success', 'User creation page');
    }

    // Store new user
    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        // default role assign (member)
        $role = Role::where('name', 'member')->first();
        $user->roles()->attach($role->id);

        return redirect()->route('users.index')->with('success', 'User Create Successful');
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('manager.users.edit', compact('user', 'roles'))->with('success', 'User Edit Page');
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update roles if provided
        if ($request->roles) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User Update Successful');
    }

    // Delete user
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User Delete Successful');
    }
}
