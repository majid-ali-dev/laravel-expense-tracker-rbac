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
            </div>
        </div>
    </div>
</div>
@endsection
