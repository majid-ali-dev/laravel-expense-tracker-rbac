@extends('layouts.app')

@section('title', 'Expense History')
@section('page_title', 'Expense History')
@section('page_subtitle', 'Combined monthly expense history for manager and super admin access.')

@section('content')
<div class="container">
    @forelse($historySections as $section)
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ $section['label'] }}</h4>
            <span class="fw-semibold">Monthly Total: Rs {{ number_format($section['total'], 2) }}</span>
        </div>

        <div class="table-responsive-wrapper">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Last Updated By</th>
                        <th>Last Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($section['expenses'] as $expense)
                    <tr>
                        <td>{{ $expense->title }}</td>
                        <td>Rs {{ number_format($expense->amount, 2) }}</td>
                        <td>{{ optional($expense->date)->format('d-m-Y') }}</td>
                        <td>{{ $expense->user->name ?? '-' }}</td>
                        <td>{{ optional($expense->created_at)->format('d-m-Y H:i:s') }}</td>
                        <td>{{ $expense->updater->name ?? '-' }}</td>
                        <td>{{ $expense->updated_by ? optional($expense->updated_at)->format('d-m-Y H:i:s') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <p class="mb-0">No expense history available.</p>
    @endforelse

    <div class="d-flex justify-content-end pt-3 border-top">
        <strong>Overall Total: Rs {{ number_format($overallTotal, 2) }}</strong>
    </div>
</div>
@endsection
