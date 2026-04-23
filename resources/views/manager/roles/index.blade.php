@extends('layouts.app')

@section('content')

<div class="page-header">
    <h4 class="mb-0">Roles</h4>
    <a href="{{ route('roles.create') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Create Role</span>
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
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
                    <div class="action-group">
                        <a href="{{ route('roles.edit',$role->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('roles.delete',$role->id) }}" method="POST" class="inline-form">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary text-danger d-inline-flex align-items-center gap-2">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $roles->links() }}

@endsection
