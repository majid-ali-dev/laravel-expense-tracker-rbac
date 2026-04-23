@extends('layouts.app')

@section('content')

<div class="page-header">
    <h4 class="mb-0">Permissions</h4>
    <a href="{{ route('permissions.create') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Create Permission</span>
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
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
                    <div class="action-group">
                        <a href="{{ route('permissions.edit',$permission->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('permissions.delete',$permission->id) }}" method="POST" class="inline-form">
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
{{ $permissions->links() }}

@endsection
