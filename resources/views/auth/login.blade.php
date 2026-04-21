@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-kicker">
    <i class="bi bi-box-arrow-in-right"></i>
    Welcome Back
</div>

<h2 class="auth-title">Sign in to your account</h2>
<p class="auth-subtitle">Access your workspace and continue tracking expenses with a clean, focused dashboard.</p>

@if ($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-sm mt-4">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('login.submit') }}" method="POST" class="mt-4">
    @csrf

    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter your email">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Enter your password">
        </div>
    </div>

    <div class="d-grid mb-4">
        <button type="submit" class="btn btn-primary btn-auth">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
    </div>

    <p class="text-center text-muted mb-0">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-decoration-none fw-semibold auth-footer-link">Create one now</a>
    </p>
</form>
@endsection
