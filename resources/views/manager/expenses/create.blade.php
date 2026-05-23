@extends('layouts.app')

@section('content')

<div class="container">

    <div class="page-header">
        <h4 class="mb-0">Add Expense</h4>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>

    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                <option value="">Select category</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" placeholder="Enter amount">
            @error('amount')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" value="{{ old('date') }}" class="form-control @error('date') is-invalid @enderror">
            @error('date')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter description (optional)" rows="3">{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success d-inline-flex align-items-center gap-2">
            <i class="bi bi-check2-circle"></i>
            <span>Save</span>
        </button>

    </form>

</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Container Styling */
    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        color: #212529;
        padding-left: 0;
        padding-right: 20px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #6c757d transparent transparent transparent;
    }

    /* Dropdown Styling */
    .select2-container .select2-dropdown {
        border-color: #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-top: 4px;
    }

    /* Search Input Styling */
    .select2-container--default .select2-search--dropdown {
        padding: 0.5rem;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .select2-search__field {
        width: 100% !important;
        padding: 0.375rem 0.75rem !important;
        margin: 0 !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        outline: none !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .select2-search__field:focus {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }

    /* Results Styling */
    .select2-results {
        max-height: 300px;
    }

    .select2-results__option {
        padding: 0.5rem 0.75rem;
        margin: 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .select2-results__option--highlighted {
        background-color: #0d6efd;
        color: white;
    }

    .select2-results__option--selected {
        background-color: #e9ecef;
        color: #212529;
    }

    /* Placeholder Styling */
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    /* Invalid state styling */
    .select2-container--default.select2-container--error .select2-selection--single {
        border-color: #dc3545;
    }

    /* Hover and Focus states */
    .select2-container--default .select2-selection--single:hover {
        border-color: #86b7fe;
    }

    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#category_id').select2({
            placeholder: 'Search or select a category...'
            , allowClear: true
            , width: '100%'
            , language: {
                searching: function() {
                    return 'Searching...';
                }
                , noResults: function() {
                    return 'No categories found';
                }
                , inputTooShort: function(args) {
                    var remainingChars = args.minimumInputLength - args.input.length;
                    return 'Type ' + remainingChars + ' more character' + (remainingChars > 1 ? 's' : '') + ' to search';
                }
            }
            , minimumInputLength: 0
            , maximumInputLength: 50
            , templateResult: formatCategory
            , templateSelection: formatCategorySelection
        });

        // Function to format category display in dropdown
        function formatCategory(category) {
            if (category.loading) {
                return category.text;
            }

            var $container = $(
                '<div class="d-flex align-items-center">' +
                '<i class="bi bi-tag me-2" style="color: #6c757d;"></i>' +
                '<span>' + category.text + '</span>' +
                '</div>'
            );

            return $container;
        }

        // Function to format selected category
        function formatCategorySelection(category) {
            return category.text || category.id;
        }

        // Handle error state for select2
        @error('category_id')
        $('#category_id').next('.select2-container').find('.select2-selection--single').css({
            'border-color': '#dc3545'
        });
        @enderror
    });

</script>
@endpush

@endsection
