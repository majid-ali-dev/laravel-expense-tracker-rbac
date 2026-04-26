@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'A single workspace powered by your assigned permissions.')

@section('content')
<div class="d-flex flex-column gap-4">
    <div>
        <span class="page-eyebrow"><i class="bi bi-grid-1x2-fill"></i> Workspace</span>
        <h1 class="page-title mt-3">Welcome back, {{ $user->name }}</h1>
        <p class="page-description">
            Your dashboard stays the same for every user, while actions and navigation are unlocked by permissions.
        </p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <span class="metric-icon"><i class="bi bi-person-badge-fill"></i></span>
                <div class="metric-value">{{ $roleNames->join(', ') ?: 'No role assigned' }}</div>
                <div class="metric-label">Assigned Roles</div>
                <p class="section-text mt-2">Roles provide the permission set used across the application.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <span class="metric-icon"><i class="bi bi-cash-stack"></i></span>
                <div class="metric-value">{{ $expenseCount }}</div>
                <div class="metric-label">Visible Expenses</div>
                <p class="section-text mt-2">The count is scoped to your access level and expense permissions.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="metric-card">
                <span class="metric-icon"><i class="bi bi-graph-up-arrow"></i></span>
                <div class="metric-value">Rs {{ number_format($totalAmount, 0) }}</div>
                <div class="metric-label">Visible Total</div>
                <p class="section-text mt-2">This summary reflects only the expense records you are allowed to view.</p>
            </div>
        </div>
    </div>

    <!-- Member Payment Details Section -->
    @if($user->hasRole('member') && !empty($paymentData))
    <div class="row g-4">
        <div class="col-12">
            <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="section-title mb-0">Your Payment Summary</h2>
                    <i class="bi bi-credit-card fs-4 text-primary"></i>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Total Amount</small>
                            <h4 class="mb-0 text-primary">Rs {{ number_format($paymentData['total_amount'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Total Paid</small>
                            <h4 class="mb-0 text-success">Rs {{ number_format($paymentData['total_paid'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Remaining</small>
                            <h4 class="mb-0 text-warning">Rs {{ number_format($paymentData['remaining'] ?? 0, 2) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="bg-light rounded-3 p-3 text-center">
                            <small class="text-muted d-block">Payment Status</small>
                            @php
                            $statusBadge = [
                            'paid' => 'bg-success',
                            'partial' => 'bg-warning',
                            'unpaid' => 'bg-danger',
                            ];
                            @endphp
                            <h4 class="mb-0">
                                <span class="badge {{ $statusBadge[$paymentData['payment_status'] ?? 'unpaid'] }}">
                                    {{ ucfirst($paymentData['payment_status'] ?? 'Unpaid') }}
                                </span>
                            </h4>
                        </div>
                    </div>
                </div>

                @if(($paymentData['payment_count'] ?? 0) > 0)
                <div class="mt-3 pt-2 border-top">
                    <small class="text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        Total payments made: {{ $paymentData['payment_count'] }} times
                        @if($paymentData['last_payment'])
                        | Last payment: {{ $paymentData['last_payment']->created_at->format('d-m-Y') }}
                        @endif
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="section-card">
                <h2 class="section-title mb-3">Quick Access</h2>
                <ul class="list-clean">
                    @if($canViewExpenses)
                    <li>
                        <span class="bullet-soft"><i class="bi bi-receipt"></i></span>
                        <div>
                            <div class="fw-semibold">Expense records</div>
                            <div class="section-text">View and manage the expense list available to your account.</div>
                        </div>
                    </li>
                    @endif

                    @if($canCreateExpenses)
                    <li>
                        <span class="bullet-soft"><i class="bi bi-plus-circle"></i></span>
                        <div>
                            <div class="fw-semibold">Create expenses</div>
                            <div class="section-text">Add new expense entries directly from the expenses section.</div>
                        </div>
                    </li>
                    @endif

                    @if($canManageUsers)
                    <li>
                        <span class="bullet-soft"><i class="bi bi-people"></i></span>
                        <div>
                            <div class="fw-semibold">User administration</div>
                            <div class="section-text">Manage accounts and keep role assignments organized.</div>
                        </div>
                    </li>
                    @endif

                    @if($canManageRoles)
                    <li>
                        <span class="bullet-soft"><i class="bi bi-shield-lock"></i></span>
                        <div>
                            <div class="fw-semibold">Roles and permissions</div>
                            <div class="section-text">Control which permissions are granted through each role.</div>
                        </div>
                    </li>
                    @endif

                    <!-- Member specific quick access -->
                    @if($user->hasRole('member'))
                    <li>
                        <span class="bullet-soft"><i class="bi bi-credit-card"></i></span>
                        <div>
                            <div class="fw-semibold">My Payments</div>
                            <div class="section-text">View your payment history and track your dues.</div>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="section-card">
                <h2 class="section-title mb-3">Permission Summary</h2>
                @if($permissions->isNotEmpty())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($permissions as $permission)
                    <span class="badge rounded-pill text-bg-light border px-3 py-2">{{ $permission }}</span>
                    @endforeach
                </div>
                @else
                <p class="section-text mb-0">No permissions are currently assigned through your roles.</p>
                @endif

                @if($canDownloadExpenses)
                <p class="section-text mt-3 mb-0">You can also export expense data from the expenses page.</p>
                @endif

                <!-- Member specific info -->
                @if($user->hasRole('member'))
                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    As a member, you can view your own expenses and track your payment status.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
