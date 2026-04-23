@extends('layouts.app')

@section('content')

<div class="container">

    <h4>My Payments</h4>

    <table class="table">
        <thead>
            <tr>
                <th>Month</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Status</th>
                <th>Pay</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ strtoupper($payment->month) }}</td>
                <td>{{ $payment->total_amount }}</td>
                <td>{{ $payment->paid_amount }}</td>
                <td>{{ $payment->remaining_amount }}</td>
                <td>{{ ucfirst($payment->status) }}</td>

                <td>
                    @if($payment->status != 'paid')
                    <form action="{{ route('payments.pay', $payment->id) }}" method="POST">
                        @csrf
                        <input type="number" name="amount" placeholder="Enter amount" required>
                        <button class="btn btn-sm btn-primary">Pay</button>
                    </form>
                    @else
                    <span class="text-success">Paid</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection
