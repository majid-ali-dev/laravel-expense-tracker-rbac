@extends('layouts.app')

@section('title', 'Create User')

@section('content')

<div class="container">
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <div class="page-header">
                <h4 class="mb-0 fw-bold">Create User</h4>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name">
                    @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email">
                    @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter phone">
                    @error('phone')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password">
                    @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Assign Roles</label>

                    @foreach($roles as $role)
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->id }}"
                            class="form-check-input"
                            {{ collect(old('roles', []))->contains($role->id) ? 'checked' : '' }}
                        >
                        <label class="form-check-label">{{ $role->name }}</label>
                    </div>
                    @endforeach
                    @error('roles')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('roles.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-success d-inline-flex align-items-center gap-2">
                    <i class="bi bi-check2-circle"></i>
                    <span>Save</span>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
