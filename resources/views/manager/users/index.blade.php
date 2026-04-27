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
                            <th>Total Amount</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
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

                            <!-- ✅ Total Amount with inline edit -->
                            <td>
                                <form action="{{ route('users.updateTotal', $user->id) }}" method="POST" class="d-flex gap-1">
                                    @csrf
                                    <input type="number" name="total_amount" value="{{ $user->total_amount }}" class="form-control form-control-sm text-center" style="width: 100px" step="0.01" min="0" {{ $user->total_paid > 0 ? 'min=' . $user->total_paid : '' }}>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </form>
                            </td>

                            <td>{{ number_format($user->total_paid, 2) }}</td>

                            @php
                            $remaining = $user->remaining;
                            $remainingClass = $remaining > 0 ? 'text-danger' : 'text-success';
                            @endphp
                            <td><span class="{{ $remainingClass }} fw-bold">{{ number_format($remaining, 2) }}</span></td>

                            <td>
                                @php
                                $status = $user->payment_status;
                                $badgeClass = match($status) {
                                'paid' => 'bg-success',
                                'partial' => 'bg-warning',
                                default => 'bg-danger'
                                };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('users.delete', $user->id) }}" method="POST" class="inline-form">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary text-danger" onclick="return confirm('Delete this user?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No users found</td>
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
