@extends('layouts.app')

@section('content')

<div class="container">

    <div class="page-header">
        <h4 class="mb-0">Add Expense</h4>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>

    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf

        <input type="text" name="title" value="{{ old('title') }}" class="form-control mb-2 @error('title') is-invalid @enderror" placeholder="Title">
        @error('title')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control mb-2 @error('amount') is-invalid @enderror" placeholder="Amount">
        @error('amount')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <input type="date" name="date" value="{{ old('date') }}" class="form-control mb-2 @error('date') is-invalid @enderror">
        @error('date')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <textarea name="description" class="form-control mb-2 @error('description') is-invalid @enderror" placeholder="Description">{{ old('description') }}</textarea>
        @error('description')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <button class="btn btn-success d-inline-flex align-items-center gap-2">
            <i class="bi bi-check2-circle"></i>
            <span>Save</span>
        </button>

    </form>

</div>

@endsection
