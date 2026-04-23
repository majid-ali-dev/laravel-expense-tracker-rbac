@extends('layouts.app')

@section('title', 'Expense History')
@section('page_title', 'Expense History')
@section('page_subtitle', 'Combined monthly expense history with complete change timeline.')

@php
$fieldLabels = [
'title' => 'Title',
'amount' => 'Amount',
'date' => 'Date',
'description' => 'Description',
];
@endphp

@section('content')
<div class="container-fluid px-0">
    @forelse($historySections as $section)
    <div class="mb-5">
        <!-- Section Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 pb-2 border-bottom">
            <h4 class="mb-2 mb-sm-0 fw-semibold">
                <span class="badge bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                    {{ $section['label'] }}
                </span>
            </h4>
            <div class="bg-success bg-opacity-10 px-4 py-2 rounded-3">
                <span class="text-success fw-semibold">
                    <i class="bi bi-calculator-fill me-1"></i>
                    Monthly Total: <span class="fw-bold">Rs {{ number_format($section['total'], 2) }}</span>
                </span>
            </div>
        </div>

        <!-- Expenses List -->
        <div class="d-flex flex-column gap-4">
            @foreach($section['expenses'] as $expense)
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <!-- Card Header -->
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                <h5 class="mb-0 fw-bold">{{ $expense->title }}</h5>
                                @if(!empty($expense->is_deleted_entry))
                                <span class="badge bg-danger">
                                    <i class="bi bi-trash3 me-1"></i>Deleted Record
                                </span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-3 text-muted small">
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-currency-rupee me-1"></i>Rs {{ number_format($expense->amount, 2) }}
                                </span>
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-calendar3 me-1"></i>{{ optional($expense->date)->format('d-m-Y') }}
                                </span>
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-person-circle me-1"></i>{{ $expense->user->name ?? '-' }}
                                </span>
                            </div>
                            @if(!empty($expense->description))
                            <div class="mt-2 text-secondary">
                                <i class="bi bi-file-text me-1"></i>{{ $expense->description }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Body - Audit History -->
                <div class="card-body px-4 pb-4">
                    <div class="mt-2">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-clock-history text-primary"></i>
                            <span class="fw-semibold text-primary">Audit Timeline</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">
                                {{ $expense->histories->count() }} changes
                            </span>
                        </div>

                        <div class="timeline-wrapper">
                            @forelse($expense->histories as $history)
                            <div class="timeline-item mb-3">
                                <div class="card bg-light border-0 rounded-3">
                                    <div class="card-body p-3">
                                        <!-- History Header -->
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                            <div>
                                                @if($history->action === 'created')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-plus-circle me-1"></i>Created
                                                </span>
                                                @elseif($history->action === 'updated')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-pencil-square me-1"></i>Updated
                                                </span>
                                                @elseif($history->action === 'deleted')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-trash me-1"></i>Deleted
                                                </span>
                                                @else
                                                <span class="badge bg-secondary">{{ ucfirst($history->action) }}</span>
                                                @endif
                                                <span class="text-muted ms-2">
                                                    by <b>{{ $history->user->name ?? 'System' }}</b>
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                {{ optional($history->created_at)->format('d-m-Y h:i A') }}
                                            </small>
                                        </div>

                                        <!-- History Content -->
                                        @if($history->action === 'created')
                                        <div class="mt-2 pt-1 text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Expense created with amount
                                            <strong>Rs {{ number_format((float) ($history->new_data['amount'] ?? 0), 2) }}</strong>
                                        </div>
                                        @elseif($history->action === 'deleted')
                                        <div class="mt-2 pt-1 text-danger">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            Expense permanently deleted from the system
                                        </div>
                                        @elseif($history->action === 'updated')
                                        @php
                                        $changedFields = collect(explode(',', $history->changed_fields ?? ''))
                                        ->filter()
                                        ->values();
                                        @endphp

                                        <div class="mt-2 pt-1">
                                            <div class="small text-muted mb-2">Changes made:</div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered text-center align-middle table-sm mb-0">
                                                    <tbody>
                                                        @foreach($changedFields as $field)
                                                        @php
                                                        $oldValue = $history->old_data[$field] ?? '-';
                                                        $newValue = $history->new_data[$field] ?? '-';

                                                        if ($field === 'amount') {
                                                        $oldValue = 'Rs ' . number_format((float) $oldValue, 2);
                                                        $newValue = 'Rs ' . number_format((float) $newValue, 2);
                                                        }

                                                        if ($field === 'date') {
                                                        $oldValue = $oldValue && $oldValue !== '-' ? \Carbon\Carbon::parse($oldValue)->format('d-m-Y') : '-';
                                                        $newValue = $newValue && $newValue !== '-' ? \Carbon\Carbon::parse($newValue)->format('d-m-Y') : '-';
                                                        }
                                                        @endphp
                                                        <tr>
                                                            <td class="field-label-cell ps-0 pe-2">
                                                                <strong class="text-secondary">{{ $fieldLabels[$field] ?? ucfirst($field) }}:</strong>
                                                            </td>
                                                            <td class="px-2">
                                                                <span class="text-danger text-decoration-line-through">{{ $oldValue }}</span>
                                                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                                                <span class="text-success fw-semibold">{{ $newValue }}</span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-info-circle fs-4"></i>
                                <p class="mb-0 mt-2">No audit history available for this expense.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <div class="bg-light rounded-4 p-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h4 class="mt-3 text-muted">No Expense History Available</h4>
            <p class="text-muted mb-0">There are no expense records to display.</p>
        </div>
    </div>
    @endforelse

    <!-- Overall Total Footer -->
    <div class="position-sticky bottom-0 bg-white pt-3 mt-4 border-top rounded-4 shadow-lg">
        <div class="d-flex justify-content-between align-items-center p-3 bg-primary bg-opacity-10 rounded-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calculator-fill text-primary fs-5"></i>
                <span class="fw-semibold text-primary">Overall Total:</span>
            </div>
            <span class="fs-4 fw-bold text-primary">Rs {{ number_format($overallTotal, 2) }}</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Optional: Add any interactive features here
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effect on timeline items
        const timelineItems = document.querySelectorAll('.timeline-item .card');
        timelineItems.forEach(item => {
            item.classList.add('transition-all');
        });
    });

</script>
@endpush
@endsection
