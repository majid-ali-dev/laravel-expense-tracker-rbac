@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h4>Roles</h4>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
</div>

<table class="table table-bordered text-center">
    <thead>
        <tr>
            <th>#</th>
            <th>Role Name</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($roles as $role)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $role->name }}</td>
            <td>
                <a href="{{ route('roles.edit',$role->id) }}" class="btn btn-sm btn-warning"><span class="material-icons">edit</span></a>

                <form action="{{ route('roles.delete',$role->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-sm btn-danger"><span class="material-icons">delete</span></button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $roles->links() }}

@endsection
