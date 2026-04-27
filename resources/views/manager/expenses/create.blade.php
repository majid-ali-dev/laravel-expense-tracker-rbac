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

        <label for="category_id" class="form-label">Category</label>
        <select id="category_id" name="category_id" class="form-select mb-2 @error('category_id') is-invalid @enderror" style="width: 100%;" required>
            <option value="">Select category</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control mb-2 @error('amount') is-invalid @enderror" placeholder="Amount">
        @error('amount')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <input type="date" name="date" value="{{ old('date') }}" class="form-control mb-2 @error('date') is-invalid @enderror">
        @error('date')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <textarea name="description" class="form-control mb-2 @error('description') is-invalid @enderror" placeholder="Description">{{ old('description') }}</textarea>
        @error('description')
        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
        @enderror

        <button class="btn btn-success d-inline-flex align-items-center gap-2">
            <i class="bi bi-check2-circle"></i>
            <span>Save</span>
        </button>

    </form>

</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
        border-radius: .375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        color: #495057;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);
    }
    .select2-container .select2-dropdown {
        border-color: #ced4da;
        border-radius: .375rem;
    }
    .select2-container--open .select2-dropdown {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .select2-results__option {
        padding: .5rem .75rem;
    }
    .select2-search__field {
        width: 100% !important;
        padding: .375rem .75rem !important;
        border: 1px solid #ced4da !important;
        border-radius: .375rem !important;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#category_id').select2({
            placeholder: 'Select category',
            width: 'resolve',
            allowClear: true,
        });
    });
</script>
@endpush

@endsection
