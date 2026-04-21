@extends('layouts.app')

@section('content')

<h4>Assign Permissions → {{ $role->name }}</h4>

<form method="POST" action="{{ route('role.permissions.update',$role->id) }}">
    @csrf

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Permission</th>
                <th>Select</th>
            </tr>
        </thead>

        <tbody>
            @foreach($permissions as $permission)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $permission->name }}</td>
                <td>
                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button class="btn btn-success">Save</button>
    <a href="{{ route('role.permissions.index') }}" class="btn btn-secondary">Back</a>

</form>

@endsection
