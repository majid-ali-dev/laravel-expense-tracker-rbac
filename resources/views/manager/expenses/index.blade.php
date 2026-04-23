@extends('layouts.app')

@section('content')

<div class="container expenses-page">

    <div class="page-header expenses-toolbar">
        <h4 class="expenses-title">Expenses</h4>

        <div class="page-actions expenses-actions">

            @if(auth()->user()->hasPermission('create-expense'))
            <a href="{{ route('expenses.create') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" title="Add">
                <i class="bi bi-plus-lg"></i>
                <span>Add</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('download-expense'))
            <a href="{{ route('expenses.download') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" title="Download">
                <i class="bi bi-download"></i>
                <span>Download</span>
            </a>
            @endif

            @if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('super_admin'))
            <a href="{{ route('expenses.history') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" title="View History">
                <i class="bi bi-clock-history"></i>
                <span>History</span>
            </a>
            @endif

        </div>
    </div>

    <div class="table-responsive mt-2">
        <table class="table table-bordered text-center align-middle expense-table">
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
                        <div class="action-group expense-actions-inner">

                            {{-- VIEW --}}
                            @if(auth()->user()->hasPermission('view-expense'))
                            <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif

                            {{-- EDIT --}}
                            @if(auth()->user()->hasPermission('edit-expense'))
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            @endif

                            {{-- DELETE --}}
                            @if(auth()->user()->hasPermission('delete-expense'))
                            <form action="{{ route('expenses.delete', $expense->id) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary text-danger d-inline-flex align-items-center gap-2" title="Delete">
                                    <i class="bi bi-trash"></i>
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
