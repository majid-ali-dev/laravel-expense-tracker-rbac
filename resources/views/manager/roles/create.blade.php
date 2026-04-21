@extends('layouts.app')

@section('content')

<h4>Create Role</h4>

<form action="{{ route('roles.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Role Name</label>
        <input type="text" name="name" class="form-control" placeholder="Enter role name">
    </div>

    <button class="btn btn-success">Save</button>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
