@extends('layouts.light.master')
@section('title', 'Product Reviews')

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
@endpush

@section('breadcrumb-title')
<h3>Product Reviews</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Reviews</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="card rounded-0 shadow-sm">
                <div class="card-header p-3">
                    <div class="row px-3 justify-content-between align-items-center">
                        <div>All Reviews</div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}"
                               class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                                Pending ({{ \App\Models\Review::where('approved', false)->count() }})
                            </a>
                            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
                               class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                                Approved ({{ \App\Models\Review::where('approved', true)->count() }})
                            </a>
                            <a href="{{ route('admin.reviews.index') }}"
                               class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                                All Reviews
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($reviews->count() > 0)
                        <form id="bulk-actions-form" method="POST" action="#" onsubmit="return false;">
                            @csrf
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-success" id="bulk-approve-btn" style="display: none;">
                                    <i class="fa fa-check"></i> Approve Selected
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" id="bulk-delete-btn" style="display: none;">
                                    <i class="fa fa-trash"></i> Delete Selected
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="select-all">
                                            </th>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Customer</th>
                                            <th>Review</th>
                                            <th>Rating</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reviews as $review)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="review_ids[]" value="{{ $review->id }}" class="review-checkbox">
                                                </td>
                                                <td>{{ $review->id }}</td>
                                                <td>
                                                    @if($review->reviewable instanceof \App\Models\Product)
                                                        <a href="{{ route('products.show', $review->reviewable) }}" target="_blank">
                                                            {{ $review->reviewable->name }}
                                                        </a>
                                                    @else
                                                        {{ class_basename($review->reviewable_type) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $review->user->name ?? 'Anonymous' }}
                                                    @if($review->user)
                                                        <br><small class="text-muted">{{ $review->user->email }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div style="max-width: 300px;">
                                                        {{ Str::limit($review->review, 100) }}
                                                        @if(strlen($review->review) > 100)
                                                            <a href="#" class="text-primary" data-toggle="modal" data-target="#reviewModal{{ $review->id }}">Read more</a>
                                                        @endif
                                                    </div>
                                                    @if($review->recommend)
                                                        <span class="badge badge-success mt-1">
                                                            <i class="fa fa-check"></i> Recommended
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $rating = $review->ratings->where('key', 'overall')->first()->value ?? 0;
                                                    @endphp
                                                    <div class="d-flex align-items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $rating)
                                                                <i class="fa fa-star text-warning"></i>
                                                            @else
                                                                <i class="far fa-star text-muted"></i>
                                                            @endif
                                                        @endfor
                                                        <span class="ml-2">{{ $rating }}/5</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($review->approved)
                                                        <span class="badge badge-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $review->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @if(!$review->approved)
                                                            <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline approve-review-form" id="approve-form-{{ $review->id }}">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success approve-btn" title="Approve" data-review-id="{{ $review->id }}" data-action="{{ route('admin.reviews.approve', $review->id) }}">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline delete-review-form" id="delete-form-{{ $review->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger delete-btn" title="Delete" data-review-id="{{ $review->id }}" data-action="{{ route('admin.reviews.destroy', $review->id) }}">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Review Modal -->
                                            <div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Full Review</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ $review->review }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $reviews->links() }}
                            </div>
                        </form>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No reviews found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Select all checkbox
        $('#select-all').on('change', function() {
            $('.review-checkbox').prop('checked', this.checked);
            toggleBulkButtons();
        });

        // Individual checkbox change
        $('.review-checkbox').on('change', function() {
            toggleBulkButtons();
            $('#select-all').prop('checked', $('.review-checkbox:checked').length === $('.review-checkbox').length);
        });

        function toggleBulkButtons() {
            const checkedCount = $('.review-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#bulk-approve-btn, #bulk-delete-btn').show();
            } else {
                $('#bulk-approve-btn, #bulk-delete-btn').hide();
            }
        }

        // Bulk approve
        $('#bulk-approve-btn').on('click', function() {
            const checkedCount = $('.review-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one review to approve.');
                return;
            }
            if (confirm('Are you sure you want to approve ' + checkedCount + ' selected review(s)?')) {
                $('#bulk-actions-form').off('submit').attr('action', '{{ route("admin.reviews.bulk-approve") }}').attr('onsubmit', '').submit();
            }
        });

        // Bulk delete
        $('#bulk-delete-btn').on('click', function() {
            const checkedCount = $('.review-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Please select at least one review to delete.');
                return;
            }
            if (confirm('Are you sure you want to delete ' + checkedCount + ' selected review(s)? This action cannot be undone.')) {
                $('#bulk-actions-form').off('submit').attr('action', '{{ route("admin.reviews.bulk-delete") }}').attr('onsubmit', '').submit();
            }
        });

        // Handle individual approve button clicks
        $(document).on('click', '.approve-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const btn = $(this);
            const action = btn.data('action') || btn.closest('form').attr('action');
            const token = btn.closest('form').find('input[name="_token"]').val();
            
            if (!action) {
                alert('Error: Could not determine approval URL.');
                return false;
            }
            
            if (confirm('Are you sure you want to approve this review?')) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: action,
                    type: 'POST',
                    data: {
                        _token: token
                    },
                    success: function(response) {
                        // Reload the page to show updated status
                        window.location.reload();
                    },
                    error: function(xhr) {
                        console.error('Approve error:', xhr);
                        alert('Error approving review: ' + (xhr.responseJSON?.message || 'Please try again.'));
                        btn.prop('disabled', false).html('<i class="fa fa-check"></i>');
                    }
                });
            }
            return false;
        });

        // Handle individual delete button clicks
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const btn = $(this);
            const action = btn.data('action') || btn.closest('form').attr('action');
            const token = btn.closest('form').find('input[name="_token"]').val();
            
            if (!action) {
                alert('Error: Could not determine delete URL.');
                return false;
            }
            
            if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: action,
                    type: 'POST',
                    data: {
                        _token: token,
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        // Reload the page to show updated status
                        window.location.reload();
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr);
                        alert('Error deleting review: ' + (xhr.responseJSON?.message || 'Please try again.'));
                        btn.prop('disabled', false).html('<i class="fa fa-trash"></i>');
                    }
                });
            }
            return false;
        });
    });
</script>
@endpush
