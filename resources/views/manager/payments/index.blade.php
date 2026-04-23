@extends('layouts.app')

@section('content')

<div class="container">

    <h4>All Payments</h4>

    <!-- 🔥 TOTAL BOX -->
    <div class="mb-3">
        <b>Total Amount:</b> {{ $totalAmount }} |
        <b>Total Paid:</b> {{ $totalPaid }} |
        <b>Remaining:</b> {{ $totalRemaining }} |
        <b>Paid Users:</b> {{ $paidCount }} |
        <b>Unpaid Users:</b> {{ $unpaidCount }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Month</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->user->name ?? '-' }}</td>
                <td>{{ strtoupper($payment->month) }}</td>
                <td>{{ $payment->total_amount }}</td>
                <td>{{ $payment->paid_amount }}</td>
                <td>{{ $payment->remaining_amount }}</td>
                <td>{{ ucfirst($payment->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection
