<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle login request
    public function login(Request $request)
    {
        // Validate login input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (Auth::attempt($request->only('email', 'password'))) {

            $user = Auth::user()->load('roles');

            // super admin first check
            if ($user->hasRole('super_admin')) {
                return redirect()->route('admin.dashboard')->with('success', 'Login Successful');
            }

            if ($user->hasRole('manager')) {
                return redirect()->route('manager.dashboard')->with('success', 'Login Successful');
            }

            if ($user->hasRole('staff')) {
                return redirect()->route('staff.dashboard')->with('success', 'Login Successful');
            }

            if ($user->hasRole('member')) {
                return redirect()->route('member.dashboard')->with('success', 'Login Successful');
            }

            // fallback if role missing
            Auth::logout();

            return back()->with('error', 'No role assigned to this user');
        }

        return back()->with('error', 'Invalid Credentials');
    }

    // Handle register request
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:5',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Assign default role = member
        $role = Role::where('name', 'member')->first();

        if ($role) {
            $user->roles()->attach($role->id);
        }

        // Auto login
        Auth::login($user);

        return redirect()->route('member.dashboard')->with('success', 'Registration Successful');
    }

    // Handle logout
    public function logout()
    {
        Auth::logout();

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}
