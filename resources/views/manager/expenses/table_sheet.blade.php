@extends('layouts.app')

@section('title', 'Expense Table Sheet')
@section('page_title', 'Expense Report Sheet')
@section('page_subtitle', 'View complete expense report with member payments')

@section('content')
<div class="container-fluid px-0">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="page-eyebrow">
                    <i class="bi bi-file-earmark-excel"></i>
                    Excel Sheet
                </span>
                <h4 class="page-title mt-2 mb-1">March - Saved Report</h4>
                <p class="page-description">Complete expense and payment summary for March</p>
            </div>
            <div>
                <a href="{{ route('expenses.download-sheet') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-download"></i> Download Excel Sheet
                </a>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Main Expenses Table -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table me-2"></i>Daily Expenses
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @php
                $groupedExpenses = [];
                $itemNames = [];

                foreach ($expenses as $expense) {
                    $dateKey = $expense->date->format('Y-m-d');
                    $itemName = trim($expense->title) ?: 'Other';

                    if (! in_array($itemName, $itemNames, true)) {
                        $itemNames[] = $itemName;
                    }

                    $groupedExpenses[$dateKey]['date'] = $expense->date->format('d/m/Y');
                    $groupedExpenses[$dateKey][$itemName] = ($groupedExpenses[$dateKey][$itemName] ?? 0) + $expense->amount;
                    $groupedExpenses[$dateKey]['total'] = ($groupedExpenses[$dateKey]['total'] ?? 0) + $expense->amount;
                }

                ksort($groupedExpenses);
                $totalExpenses = $expenses->sum('amount');
                $itemTotals = array_fill_keys($itemNames, 0);
                foreach ($groupedExpenses as $expenseRow) {
                    foreach ($itemNames as $itemName) {
                        $itemTotals[$itemName] += $expenseRow[$itemName] ?? 0;
                    }
                }
                @endphp
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            @foreach($itemNames as $itemName)
                            <th>{{ $itemName }}</th>
                            @endforeach
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedExpenses as $expenseRow)
                        <tr>
                            <td>{{ $expenseRow['date'] }}</td>
                            @foreach($itemNames as $itemName)
                            <td>{{ isset($expenseRow[$itemName]) ? number_format($expenseRow[$itemName], 2) : '' }}</td>
                            @endforeach
                            <td>{{ number_format($expenseRow['total'] ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="{{ count($itemNames) + 2 }}">&nbsp;</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-warning fw-semibold">
                            <td>Grand Total</td>
                            @foreach($itemNames as $itemName)
                            <td>{{ number_format($itemTotals[$itemName] ?? 0, 2) }}</td>
                            @endforeach
                            <td>{{ number_format($totalExpenses, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Members Summary -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-people me-2"></i>Members Payment Summary
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Member Name</th>
                            <th>Paid Amount (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $grandPaid = 0;
                        @endphp
                        @foreach($memberTotals as $member)
                        @php
                        $grandPaid += $member['total_paid'];
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $member['name'] }}</td>
                            <td class="text-success">Rs {{ number_format($member['total_paid'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th class="text-end">Grand Total:</th>
                            <th>Rs {{ number_format($grandPaid, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Final Totals -->
    @php
    $remainingBalance = $totalMemberPaid - $totalExpenses;
    @endphp
    <div class="row g-3">
        <div class="col-md-4">
            <div class="metric-card text-center">
                <div class="metric-label">Total Expenses</div>
                <div class="metric-value text-primary">Rs {{ number_format($totalExpenses, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card text-center">
                <div class="metric-label">Total Member Paid Amount</div>
                <div class="metric-value text-success">Rs {{ number_format($totalMemberPaid, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card text-center">
                <div class="metric-label">{{ $remainingBalance < 0 ? 'Extra Balance' : 'Remaining Balance' }}</div>
                <div class="metric-value text-warning">Rs {{ number_format(abs($remainingBalance), 2) }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
