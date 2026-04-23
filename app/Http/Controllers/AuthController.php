<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email field is required.',
            'email.email' => 'Email must be a valid email address.',
            'password.required' => 'Password field is required.',
            'password.string' => 'Password must be a valid text value.',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Login Successful');
        }

        return back()->with('error', 'Invalid Credentials');
    }

    // Handle register request
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:5'],
        ], [
            'name.required' => 'Name field is required.',
            'name.string' => 'Name must be a valid text value.',
            'name.max' => 'Name may not be greater than 255 characters.',
            'email.required' => 'Email field is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone field is required.',
            'phone.string' => 'Phone must be a valid text value.',
            'phone.max' => 'Phone may not be greater than 255 characters.',
            'password.required' => 'Password field is required.',
            'password.string' => 'Password must be a valid text value.',
            'password.min' => 'Password must be at least 5 characters.',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        $role = Role::where('name', 'member')->first();

        if ($role) {
            $user->roles()->attach($role->id);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registration Successful');
    }

    // Handle logout
    public function logout()
    {
        Auth::logout();

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}
