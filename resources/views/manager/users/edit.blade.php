@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="container">
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <h4 class="mb-4 fw-bold">Edit User</h4>

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
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
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>

@endsection
