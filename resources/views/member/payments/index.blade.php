@extends('layouts.app')

@section('title', 'My Payments')
@section('page_title', 'My Payments')
@section('page_subtitle', 'View your payment summary and complete payment history.')

@section('content')
<div class="container-fluid px-0">
    <div class="page-header">
        <div>
            <span class="page-eyebrow">
                <i class="bi bi-wallet2"></i>
                My Payments
            </span>
            <h4 class="page-title mt-3 mb-2">Payment Summary</h4>
            <p class="page-description">View your assigned amount, total paid, remaining balance, and current status.</p>
        </div>
    </div>

    <!-- Payment Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Total Amount</div>
                <div class="metric-value text-primary">Rs {{ number_format($user->total_amount ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Total Paid</div>
                <div class="metric-value text-success">Rs {{ number_format($user->total_paid ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Remaining</div>
                <div class="metric-value text-warning">Rs {{ number_format($user->remaining ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Status</div>
                <div class="metric-value">
                    @php
                    $statusClasses = [
                    'paid' => 'bg-success text-white',
                    'partial' => 'bg-warning text-dark',
                    'unpaid' => 'bg-danger text-white',
                    ];
                    @endphp
                    <span class="badge rounded-pill {{ $statusClasses[$user->payment_status ?? 'unpaid'] }} p-2">
                        {{ ucfirst($user->payment_status ?? 'Unpaid') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="card shadow-sm rounded-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-clock-history me-2"></i>Payment History
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Month</th>
                            <th>Amount Paid (Rs)</th>
                            <th>Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->payments as $index => $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $payment->created_at->format('d-m-Y h:i A') }}</td>
                            <td>{{ ucfirst($payment->month) }}</td>
                            <td class="text-success fw-semibold">Rs {{ number_format($payment->paid_amount, 2) }}</td>
                            <td>
                                @php
                                $updatedBy = \App\Models\User::find($payment->updated_by);
                                @endphp
                                {{ $updatedBy->name ?? 'System' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No payment records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Timeline View (Alternative Style) -->
    @if($user->payments->isNotEmpty())
    <div class="card shadow-sm rounded-4 mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-diagram-3 me-2"></i>Payment Timeline
            </h5>
        </div>
        <div class="card-body">
            <div class="timeline-wrapper">
                @foreach($user->payments as $payment)
                <div class="timeline-item mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-check-circle-fill text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Rs {{ number_format($payment->paid_amount, 2) }}</div>
                                <small class="text-muted">{{ ucfirst($payment->month) }} payment</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">{{ $payment->created_at->format('d-m-Y h:i A') }}</div>
                            @php
                            $updatedBy = \App\Models\User::find($payment->updated_by);
                            @endphp
                            <small class="text-muted">by {{ $updatedBy->name ?? 'System' }}</small>
                        </div>
                    </div>
                </div>
                @if(!$loop->last)
                <hr class="my-2">
                @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>


@endsection
