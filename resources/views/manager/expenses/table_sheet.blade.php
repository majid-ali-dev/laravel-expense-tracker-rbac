@extends('layouts.app')

@section('title', 'Expense Table Sheet')
@section('page_title', 'Expense Report Sheet')

@section('content')

@php
    use Carbon\Carbon;
    $monthLabel = Carbon::parse($from)->format('d M Y') . ' - ' . Carbon::parse($to)->format('d M Y');
@endphp

<div class="container-fluid px-0">

    {{-- HEADER --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

           <div class="no-select">
                <h4 class="page-title fw-bold">
                Expense Report
                <small style="color:#1a74c4">({{ $monthLabel }})</small>
                </h4>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('expenses.download-sheet', ['from' => $from, 'to' => $to]) }}"
                   class="btn btn-success btn-lg">
                    <i class="bi bi-download"></i> Download Sheet
                </a>

                <a href="{{ route('expenses.index') }}"
                   class="btn btn-outline-secondary btn-lg">
                    Back
                </a>
            </div>

        </div>
    </div>

    {{-- FILTER --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('expenses.table-sheet') }}"
                  class="d-flex gap-3 align-items-end flex-wrap">

                <div>
                    <label class="form-label">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control">
                </div>

                <div>
                    <label class="form-label">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control">
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">
                        Filter
                    </button>

                    <a href="{{ route('expenses.table-sheet') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>

            </form>

        </div>
    </div>

    {{-- DAILY EXPENSES --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>Daily Expenses</h5>
        </div>

        <div class="card-body p-0">

            @php
                $grouped = [];
                $items = [];

                foreach ($expenses as $expense) {
                    $date = $expense->date->format('Y-m-d');
                    $title = $expense->title ?: 'Other';

                    if (!in_array($title, $items)) {
                        $items[] = $title;
                    }

                    $grouped[$date]['date'] = $expense->date->format('d/m/Y');
                    $grouped[$date][$title] = ($grouped[$date][$title] ?? 0) + $expense->amount;
                    $grouped[$date]['total'] = ($grouped[$date]['total'] ?? 0) + $expense->amount;
                }

                krsort($grouped);

                $itemTotals = array_fill_keys($items, 0);
                foreach ($grouped as $row) {
                    foreach ($items as $item) {
                        $itemTotals[$item] += $row[$item] ?? 0;
                    }
                }
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Date</th>
                            @foreach($items as $item)
                                <th>{{ $item }}</th>
                            @endforeach
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($grouped as $row)
                            <tr>
                                <td>{{ $row['date'] }}</td>

                                @foreach($items as $item)
                                    <td>{{ number_format($row[$item] ?? 0, 2) }}</td>
                                @endforeach

                                <td>{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="table-warning fw-bold">
                            <td>Grand Total</td>

                            @foreach($items as $item)
                                <td>{{ number_format($itemTotals[$item], 2) }}</td>
                            @endforeach

                            <td>{{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

    {{-- MEMBERS SUMMARY (NOW FIXED - PAYMENT BASED) --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>Members Summary</h5>
        </div>

        <div class="card-body">

            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Paid</th>
                    </tr>
                </thead>

                <tbody>
                    @php $totalPaid = 0; @endphp

                    @foreach($memberTotals as $m)
                        @php $totalPaid += $m['total_paid']; @endphp
                        <tr>
                            <td>{{ $m['name'] }}</td>
                            <td class="text-success fw-bold">
                                Rs {{ number_format($m['total_paid'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-light fw-bold">
                        <th>Total</th>
                        <th class="text-success">
                            Rs {{ number_format($totalPaid, 2) }}
                        </th>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>

    {{-- FINAL SUMMARY CARDS --}}
    @php $balance = $totalPaid - $totalExpenses; @endphp

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card p-3 text-center border-primary shadow-sm">
                <h6 class="text-primary">Total Expenses</h6>
                <h4 class="text-primary">Rs {{ number_format($totalExpenses, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 text-center border-success shadow-sm">
                <h6 class="text-success">Total Paid</h6>
                <h4 class="text-success">Rs {{ number_format($totalPaid, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 text-center border-warning shadow-sm">
                <h6 class="text-warning">
                    {{ $balance < 0 ? 'Remaining Balance' : 'Extra Balance' }}
                </h6>
                <h4 class="text-warning">
                    Rs {{ number_format(abs($balance), 2) }}
                </h4>
            </div>
        </div>

    </div>

</div>
@endsection