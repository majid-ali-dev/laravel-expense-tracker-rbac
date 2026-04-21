@extends('layouts.app')

@section('content')

<h4>Create Permission</h4>

<form action="{{ route('permissions.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Permission Name</label>
        <input type="text" name="name" class="form-control" placeholder="Enter permission name">
    </div>

    <button class="btn btn-success">Save</button>
    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
