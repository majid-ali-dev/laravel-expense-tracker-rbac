@extends('layouts.app')

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

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Total Amount</div>
                <div class="metric-value">{{ number_format($user->total_amount, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Total Paid</div>
                <div class="metric-value">{{ number_format($user->total_paid, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Remaining</div>
                <div class="metric-value">{{ number_format($user->remaining, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Status</div>
                <div class="metric-value">{{ ucfirst($user->payment_status) }}</div>
            </div>
        </div>
    </div>

    <div class="section-card">
        <table class="table table-borderless mb-0">
            <tr>
                <th class="field-label-cell">Total</th>
                <td>{{ number_format($user->total_amount, 2) }}</td>
            </tr>
            <tr>
                <th class="field-label-cell">Paid</th>
                <td>{{ number_format($user->total_paid, 2) }}</td>
            </tr>
            <tr>
                <th class="field-label-cell">Remaining</th>
                <td>{{ number_format($user->remaining, 2) }}</td>
            </tr>
            <tr>
                <th class="field-label-cell">Status</th>
                <td>{{ ucfirst($user->payment_status) }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
