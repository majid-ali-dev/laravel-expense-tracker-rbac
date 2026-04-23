@extends('layouts.app')

@section('title', 'Role Permissions')

@section('content')

<div class="page-header">
    <h4 class="mb-0">Roles</h4>
</div>

<div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
        <thead>
            <tr>
                <th>#</th>
                <th>Role</th>
                <th>Permissions</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->permissions->count() }}</td>
                <td>
                    <a href="{{ route('role.permissions.edit', $role->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <i class="bi bi-shield-check"></i>
                        <span>Assign Permissions</span>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $roles->links() }}

@endsection
