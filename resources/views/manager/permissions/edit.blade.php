@extends('layouts.app')

@section('content')

<h4>Edit Permission</h4>

<form action="{{ route('permissions.update',$permission->id) }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Permission Name</label>
        <input type="text" name="name" class="form-control" value="{{ $permission->name }}">
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection
