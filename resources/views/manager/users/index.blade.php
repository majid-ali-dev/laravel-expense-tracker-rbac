@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')

<div class="container-fluid">

    <div class="page-header">
        <h4 class="mb-0 fw-bold">Users List</h4>
        <a href="{{ route('users.create') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Add User</span>
        </a>
    </div>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('users.delete', $user->id) }}" method="POST" class="inline-form">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary text-danger d-inline-flex align-items-center gap-2">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        </div>
    </div>

</div>

@endsection
