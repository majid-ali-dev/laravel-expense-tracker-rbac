@extends('layouts.app')

@section('title', 'Add Payment for ' . $user->name)
@section('page_title', 'Add Payment')
@section('page_subtitle', 'Add payment for ' . $user->name)

@section('content')
<div class="container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="mb-4 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-person-circle fs-1 text-primary"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope me-1"></i> {{ $user->email }}<br>
                            <i class="bi bi-phone me-1"></i> {{ $user->phone ?? 'No phone number' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <small class="text-muted d-block mb-1">Total Amount</small>
                        <h3 class="mb-0 text-primary">Rs {{ number_format($user->total_amount, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <small class="text-muted d-block mb-1">Total Paid</small>
                        <h3 class="mb-0 text-success">Rs {{ number_format($user->total_paid, 2) }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded-3 p-3 text-center">
                        <small class="text-muted d-block mb-1">Remaining Balance</small>
                        <h3 class="mb-0 text-warning">Rs {{ number_format($user->remaining, 2) }}</h3>
                    </div>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($user->remaining > 0)
            <form action="{{ route('payments.pay', $user) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="paid_amount" class="form-label fw-semibold">Payment Amount (Rs)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">Rs</span>
                        <input type="number" name="paid_amount" id="paid_amount" class="form-control form-control-lg @error('paid_amount') is-invalid @enderror" step="0.01" min="0.01" max="{{ $user->remaining }}" placeholder="Enter amount" value="{{ old('paid_amount', $user->remaining) }}" required>
                        <button class="btn btn-outline-secondary" type="button" id="fillRemaining">
                            Fill Remaining
                        </button>
                    </div>
                    <small class="text-muted">Maximum allowed: Rs {{ number_format($user->remaining, 2) }}</small>
                    @error('paid_amount')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-check-lg"></i> Submit Payment
                    </button>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary px-4 py-2">
                        <i class="bi bi-x-lg"></i> Cancel
                    </a>
                </div>
            </form>
            @else
            <div class="alert alert-success text-center">
                <i class="bi bi-check-circle-fill fs-4"></i>
                <h5 class="mt-2">Payment Complete!</h5>
                <p class="mb-0">This user has fully paid their dues. Total paid: Rs {{ number_format($user->total_paid, 2) }}</p>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('payments.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to Payments
                </a>
            </div>
            @endif
        </div>
    </div>

    @if($user->payments->isNotEmpty())
    <div class="card shadow-sm rounded-4 mt-4">
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
                            <th>Date & Time</th>
                            <th>Month</th>
                            <th>Amount Paid (Rs)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d-m-Y h:i A') }}</td>
                            <td>{{ ucfirst($payment->month) }}</td>
                            <td class="text-success fw-semibold">Rs {{ number_format($payment->paid_amount, 2) }}</td>
                            <td>
                                @if(auth()->user()->hasPermission('create-payment'))
                                <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.getElementById('fillRemaining') ? .addEventListener('click', function() {
        const remaining = {
            {
                $user - > remaining
            }
        };
        const input = document.getElementById('paid_amount');
        if (input) {
            input.value = remaining;
        }
    });

</script>
@endsection
