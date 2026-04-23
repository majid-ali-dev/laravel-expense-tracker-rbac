@extends('layouts.app')

@section('title', 'Assign Permissions')

@section('content')

<div class="page-header">
    <h4 class="mb-0">Assign Permissions -> {{ $role->name }}</h4>
    <a href="{{ route('role.permissions.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back</span>
    </a>
</div>

<form method="POST" action="{{ route('role.permissions.update', $role->id) }}">
    @csrf

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Permission</th>
                    <th>Select</th>
                </tr>
            </thead>

            <tbody>
                @foreach($permissions as $permission)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->id }}"
                            {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                        >
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @error('permissions')
    <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
    @enderror
    @error('permissions.*')
    <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
    @enderror

    <button class="btn btn-success d-inline-flex align-items-center gap-2">
        <i class="bi bi-check2-circle"></i>
        <span>Save</span>
    </button>
</form>

@endsection
