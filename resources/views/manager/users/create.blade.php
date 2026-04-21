@extends('layouts.app')

@section('title', 'Create User')

@section('content')

<div class="container">

    <div class="card shadow-sm rounded-3">
        <div class="card-body">

            <h4 class="mb-4 fw-bold">Create User</h4>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter name">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="Enter phone">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password">
                </div>

                <button class="btn btn-success">Save</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>

            </form>

        </div>
    </div>

</div>

@endsection
