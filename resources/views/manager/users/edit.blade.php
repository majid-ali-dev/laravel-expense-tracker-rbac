@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="container">
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <div class="page-header">
                <h4 class="mb-0 fw-bold">Edit User</h4>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
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
                            {{ collect(old('roles', $user->roles->pluck('id')->all()))->contains($role->id) ? 'checked' : '' }}
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

                <button class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="bi bi-check2-circle"></i>
                    <span>Update</span>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
