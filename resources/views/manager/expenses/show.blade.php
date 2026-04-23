@extends('layouts.app')

@section('content')

<div class="container">

    <div class="page-header">
        <h4 class="mb-0">Expense Details</h4>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <tr>
                <th>User</th>
                <td>{{ $expense->user->name }}</td>
            </tr>

            <tr>
                <th>Title</th>
                <td>{{ $expense->title }}</td>
            </tr>

            <tr>
                <th>Amount</th>
                <td>Rs {{ $expense->amount }}</td>
            </tr>

            <tr>
                <th>Date</th>
                <td>{{ $expense->date }}</td>
            </tr>

            <tr>
                <th>Description</th>
                <td>{{ $expense->description }}</td>
            </tr>
        </table>
    </div>

</div>

@endsection
