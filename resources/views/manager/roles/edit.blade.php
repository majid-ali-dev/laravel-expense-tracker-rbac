@extends('layouts.app')

@section('content')

<h4>Edit Role</h4>

<form action="{{ route('roles.update',$role->id) }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Role Name</label>
        <input type="text" name="name" class="form-control" value="{{ $role->name }}">
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
