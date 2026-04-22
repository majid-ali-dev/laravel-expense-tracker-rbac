@extends('layouts.app')

@section('content')

<div class="container expenses-page">

    <div class="d-flex justify-content-between align-items-center flex-wrap expenses-toolbar">
        <h4 class="expenses-title">Expenses</h4>

        <div class="expenses-actions">

            @if(auth()->user()->hasPermission('create-expense'))
            <a href="{{ route('expenses.create') }}" class="btn btn-dark" title="Add">
                <span class="material-icons">+</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('download-expense'))
            <a href="{{ route('expenses.download') }}" class="btn btn-success" title="Download">
                <span class="material-icons">download</span>
            </a>
            @endif

            @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('super_admin'))
            <a href="{{ route('expenses.history') }}" class="btn btn-secondary" title="View History">
                <span class="material-icons">history</span>
            </a>
            @endif

        </div>
    </div>

    <div class="table-responsive-wrapper">
        <table class="table table-bordered expense-table mt-2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $expense->user->name }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>Rs {{ $expense->amount }}</td>
                    <td>{{ $expense->date }}</td>
                    <td class="expense-description">{{ $expense->description }}</td>
                    <td>
                        <div class="expense-actions-inner" style="display:flex; align-items:center; justify-content:center; gap:0.5rem; flex-wrap:nowrap;">

                            {{-- VIEW --}}
                            @if(auth()->user()->hasPermission('view-expense'))
                            <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm btn-light border" title="View">
                                <span class="material-icons">visibility</span>
                            </a>
                            @endif

                            {{-- EDIT --}}
                            @if(auth()->user()->hasPermission('edit-expense'))
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-light border" title="Edit">
                                <span class="material-icons">edit</span>
                            </a>
                            @endif

                            {{-- DELETE --}}
                            @if(auth()->user()->hasPermission('delete-expense'))
                            <form action="{{ route('expenses.delete', $expense->id) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                                @csrf
                                <button class="btn btn-sm btn-light border text-danger" title="Delete">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
