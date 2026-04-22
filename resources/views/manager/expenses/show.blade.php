@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Expense Details</h4>

    <table class="table table-bordered">
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

    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>

</div>

@endsection
