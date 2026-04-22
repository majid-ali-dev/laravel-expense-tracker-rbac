@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Edit Expense</h4>

    <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
        @csrf

        <input type="text" name="title" value="{{ $expense->title }}" class="form-control mb-2">

        <input type="number" name="amount" value="{{ $expense->amount }}" class="form-control mb-2">

        <input type="date" name="date" value="{{ $expense->date }}" class="form-control mb-2">

        <textarea name="description" class="form-control mb-2">{{ $expense->description }}</textarea>

        <button class="btn btn-primary">Update</button>

    </form>

</div>

@endsection
