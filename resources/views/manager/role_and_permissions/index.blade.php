@extends('layouts.app')

@section('content')

<h4>Roles</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($roles as $role)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $role->name }}</td>
            <td>
                <a href="{{ route('role.permissions.edit',$role->id) }}" class="btn btn-sm btn-primary">
                    Assign Permissions
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $roles->links() }}

@endsection
