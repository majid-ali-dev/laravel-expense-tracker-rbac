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
        $users = User::with('roles', 'payments')
            ->where('id', '!=', auth()->id())
            ->whereHas('roles', function ($q) {
                $q->where('name', 'member');
            })
            ->simplePaginate(5);

        return view('manager.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('manager.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:5'],
            'total_amount' => ['nullable', 'numeric', 'min:0'], // ✅ Add total amount validation
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'total_amount' => $validated['total_amount'] ?? 0, // ✅ Save total amount
        ]);

        $roleIds = $validated['roles'] ?? [];

        if (empty($roleIds)) {
            $memberRoleId = Role::where('name', 'member')->value('id');
            if ($memberRoleId) {
                $roleIds = [$memberRoleId];
            }
        }

        $user->roles()->sync($roleIds);

        return redirect()->route('users.index')->with('success', 'User Created Successfully');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::orderBy('name')->get();

        return view('manager.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:255'],
            'total_amount' => ['nullable', 'numeric', 'min:0'], // ✅ Allow update total amount
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'total_amount' => $validated['total_amount'] ?? $user->total_amount, // ✅ Update total amount if provided
        ]);

        $user->roles()->sync($validated['roles'] ?? []);

        return redirect()->route('users.index')->with('success', 'User Updated Successfully');
    }

    // ✅ New method to update only total amount from users list
    public function updateTotal(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'total_amount' => ['required', 'numeric', 'min:0'],
        ]);

        // Check if new total is less than already paid amount
        if ($request->total_amount < $user->total_paid) {
            return back()->with('error', 'Total amount cannot be less than already paid amount (₹'.number_format($user->total_paid, 2).')');
        }

        $user->update([
            'total_amount' => $request->total_amount,
        ]);

        return back()->with('success', 'Total amount updated successfully');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User Deleted Successfully');
    }
}
