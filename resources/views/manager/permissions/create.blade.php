@extends('layouts.app')

@section('content')

<div class="page-header">
    <h4 class="mb-0">Create Permission</h4>
    <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back</span>
    </a>
</div>

<form action="{{ route('permissions.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Permission Name</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter permission name">
        @error('name')
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-success d-inline-flex align-items-center gap-2">
        <i class="bi bi-check2-circle"></i>
        <span>Save</span>
    </button>
</form>

@endsection
