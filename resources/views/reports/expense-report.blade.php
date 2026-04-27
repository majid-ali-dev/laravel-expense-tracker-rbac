@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Monthly Expense Report</h4>
                <small class="text-muted">{{ $reportMonth }} | {{ $rangeLabel }}</small>
            </div>
            <a href="{{ route('reports.expense.download', request()->query()) }}" class="btn btn-primary">
                <i class="bi bi-download me-1"></i> Download Excel
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('reports.expense.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Member</label>
                    <select name="member_id" class="form-select">
                        <option value="">All Members</option>
                        @foreach($membersList as $memberOption)
                            <option value="{{ $memberOption->id }}" {{ request('member_id') == $memberOption->id ? 'selected' : '' }}>
                                {{ $memberOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button type="submit" class="btn btn-success">Filter</button>
                </div>
            </form>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <h6 class="text-uppercase text-muted">Grocery Expenses</h6>
                            <h4 class="mb-0">Rs {{ number_format($financialSummary['total_grocery_expenses'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success h-100">
                        <div class="card-body">
                            <h6 class="text-uppercase text-muted">Member Collection</h6>
                            <h4 class="mb-0">Rs {{ number_format($financialSummary['total_member_collection'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning h-100">
                        <div class="card-body">
                            <h6 class="text-uppercase text-muted">Remaining Balance</h6>
                            <h4 class="mb-0">Rs {{ number_format($financialSummary['remaining_balance'], 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-danger h-100">
                        <div class="card-body">
                            <h6 class="text-uppercase text-muted">Extra Balance</h6>
                            <h4 class="mb-0">Rs {{ number_format($financialSummary['extra_balance'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">A. Daily Expense Log</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Milk</th>
                                <th>Water</th>
                                <th>Item Name</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Total Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyRows as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td>Rs {{ number_format($row['milk'], 2) }}</td>
                                    <td>Rs {{ number_format($row['water'], 2) }}</td>
                                    <td>{{ $row['item_name'] }}</td>
                                    <td>Rs {{ number_format($row['amount'], 2) }}</td>
                                    <td>{{ $row['category'] }}</td>
                                    <td>Rs {{ number_format($row['total_day_expense'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No daily expenses found for this range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">B. Weekly Grocery Summary</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Week</th>
                                <th>Date Range</th>
                                <th>Total Grocery</th>
                                <th>Per Member Deduction</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weeklySummary as $week)
                                <tr>
                                    <td>Week {{ $week['week'] }}</td>
                                    <td>{{ $week['start_date'] }} - {{ $week['end_date'] }}</td>
                                    <td>Rs {{ number_format($week['total_grocery'], 2) }}</td>
                                    <td>Rs {{ number_format($week['per_member_deduction'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No grocery data available for the selected range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">C. Member Statement</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Member Name</th>
                                <th>Total Assigned</th>
                                <th>Weekly Deductions</th>
                                <th>Total Paid</th>
                                <th>Remaining</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($members as $member)
                                <tr>
                                    <td>{{ $member['name'] }}</td>
                                    <td>Rs {{ number_format($member['total_assigned'], 2) }}</td>
                                    <td>{{ $member['weekly_deductions'] }}</td>
                                    <td>Rs {{ number_format($member['paid_amount'], 2) }}</td>
                                    <td>Rs {{ number_format($member['remaining'], 2) }}</td>
                                    <td class="text-capitalize">{{ $member['status'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No member records match this filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-4">
                <h5 class="mb-3">D. Final Balance Sheet</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-secondary h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted">Total Grocery Expenses</h6>
                                <p class="display-6 mb-0">Rs {{ number_format($financialSummary['total_grocery_expenses'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-secondary h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted">Total Member Collection</h6>
                                <p class="display-6 mb-0">Rs {{ number_format($financialSummary['total_member_collection'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted">Remaining Balance</h6>
                                <p class="display-6 mb-0">Rs {{ number_format($financialSummary['remaining_balance'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <h6 class="text-uppercase text-muted">Extra Balance</h6>
                                <p class="display-6 mb-0">Rs {{ number_format($financialSummary['extra_balance'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
