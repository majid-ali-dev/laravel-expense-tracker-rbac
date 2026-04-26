@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">
                <i class="bi bi-wallet2"></i>
                Payment Summary
            </span>
            <h4 class="page-title mt-3 mb-2">All Member Payments</h4>
            <p class="page-description">Track total, paid, remaining, and update payment entries from one screen.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Total Amount</div>
                <div class="metric-value">{{ number_format($totalAmount, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Total Paid</div>
                <div class="metric-value">{{ number_format($totalPaid, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Remaining</div>
                <div class="metric-value">{{ number_format($totalRemaining, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Paid / Partial / Unpaid</div>
                <div class="metric-value">{{ $paidCount }} / {{ $partialCount }} / {{ $unpaidCount }}</div>
            </div>
        </div>
    </div>

    <div class="table-responsive-wrapper">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Total (Rs)</th>
                    <th>Paid (Rs)</th>
                    <th>Remaining (Rs)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $user->name }}</div>
                    </td>
                    <td>{{ number_format($user->total_amount, 2) }}</td>
                    <td>{{ number_format($user->total_paid, 2) }}</td>
                    <td>{{ number_format($user->remaining, 2) }}</td>
                    <td>
                        @php
                        $statusClasses = [
                        'paid' => 'bg-success text-white',
                        'partial' => 'bg-warning text-dark',
                        'unpaid' => 'bg-danger text-white',
                        ];
                        @endphp
                        <span class="badge rounded-pill {{ $statusClasses[$user->payment_status] ?? 'bg-secondary' }}">
                            {{ ucfirst($user->payment_status) }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            @if(auth()->user()->hasPermission('create-payment'))
                            @if($user->payment_status == 'paid')
                            <a href="{{ route('payments.add', $user) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> View History
                            </a>
                            @else
                            <a href="{{ route('payments.add', $user) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-circle"></i> Add Payment
                            </a>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No member payment records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
