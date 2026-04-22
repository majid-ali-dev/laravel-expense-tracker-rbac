@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Add Expense</h4>

    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf

        <input type="text" name="title" class="form-control mb-2" placeholder="Title">

        <input type="number" name="amount" class="form-control mb-2" placeholder="Amount">

        <input type="date" name="date" class="form-control mb-2">

        <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>

        <button class="btn btn-success">Save</button>

    </form>

</div>

@endsection
