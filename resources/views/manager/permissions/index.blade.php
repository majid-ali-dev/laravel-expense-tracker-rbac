@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h4>Permissions</h4>
    <a href="{{ route('permissions.create') }}" class="btn btn-primary">Create Permission</a>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Permission Name</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($permissions as $permission)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $permission->name }}</td>
            <td>
                <a href="{{ route('permissions.edit',$permission->id) }}" class="btn btn-sm btn-warning">Edit</a>

                <form action="{{ route('permissions.delete',$permission->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $permissions->links() }}

@endsection
