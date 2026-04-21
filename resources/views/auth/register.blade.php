@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="auth-kicker">
    <i class="bi bi-person-plus"></i>
    Create Account
</div>

<h2 class="auth-title">Set up your workspace access</h2>
<p class="auth-subtitle">Create your account to start using the expense tracker in a more organized, professional environment.</p>

@if ($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-sm mt-4">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('register.submit') }}" method="POST" class="mt-4">
    @csrf

    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter your full name">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter your email">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Create a password">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Phone</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
            <input type="phone" name="phone" class="form-control" placeholder="Enter your phone number">
        </div>
    </div>

    <div class="d-grid mb-4">
        <button type="submit" class="btn btn-primary btn-auth">
            <i class="bi bi-person-plus me-2"></i>Register
        </button>
    </div>

    <p class="text-center text-muted mb-0">
        Already have an account?
        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold auth-footer-link">Sign in instead</a>
    </p>
</form>
@endsection
