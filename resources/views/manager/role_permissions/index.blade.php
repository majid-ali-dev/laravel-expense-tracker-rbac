@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<h4>Roles & Permissions</h4>

<table class="table table-bordered">
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
            <td>{{ $role->permissions->pluck('name')->join(', ') ?: 'No permissions assigned' }}</td>
            <td>
                <a href="{{ route('role.permissions.edit', $role->id) }}" class="btn btn-sm btn-primary">
                    <span class="material-icons">edit</span>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $roles->links() }}
@endsection
