@extends('layouts.light.master')

@section('title', 'Transaction Categories')

@section('breadcrumb-title')
    <h3>Transaction Categories</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
<div class="container-fluid mb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="row">
        <!-- New Category Form -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border" style="background: #ffffff;">
                <div class="card-header p-3 bg-light border-bottom">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-plus-circle text-primary mr-1"></i> Create Category</h5>
                </div>
                <div class="card-body p-3 text-dark">
                    <form action="{{ route('admin.accounting.categories.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="font-weight-bold text-dark">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control text-dark font-weight-bold" placeholder="e.g. Utility Bills, Office Snacks, Facebook Ads, Dollar Cost, Staff Salary" required>
                        </div>
                        <div class="form-group">
                            <label for="type" class="font-weight-bold text-dark">Category Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control text-dark font-weight-bold" required>
                                <option value="expense">Expense Category</option>
                                <option value="income">Income Category</option>
                                <option value="both">Both (Expense & Income)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description" class="font-weight-bold text-dark">Description</label>
                            <textarea name="description" id="description" rows="2" class="form-control text-dark" placeholder="Optional category description or notes"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fa fa-plus mr-1"></i> Add Category</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Categories List -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border" style="background: #ffffff;">
                <div class="card-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-list text-primary mr-1"></i> Existing Categories ({{ count($categories) }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-dark font-weight-bold">Category Name</th>
                                    <th class="text-dark font-weight-bold">Type</th>
                                    <th class="text-dark font-weight-bold">Description</th>
                                    <th class="text-center text-dark font-weight-bold" style="width: 110px;">Used Entries</th>
                                    <th class="text-center text-dark font-weight-bold" style="width: 100px;">Status</th>
                                    <th class="text-center text-dark font-weight-bold" style="width: 90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td class="font-weight-bold text-dark">{{ $category->name }}</td>
                                        <td>
                                            @if($category->type === 'expense')
                                                <span class="badge badge-warning px-2 py-1 font-weight-bold">Expense</span>
                                            @elseif($category->type === 'income')
                                                <span class="badge badge-success px-2 py-1 font-weight-bold">Income</span>
                                            @else
                                                <span class="badge badge-info px-2 py-1 font-weight-bold">Both</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $category->description ?? '—' }}</td>
                                        <td class="text-center font-roboto font-weight-bold text-dark">{{ $category->journal_entry_items_count }}</td>
                                        <td class="text-center">
                                            @if($category->is_active)
                                                <span class="badge badge-success px-2 py-1">Active</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.accounting.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-xs" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No categories created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
