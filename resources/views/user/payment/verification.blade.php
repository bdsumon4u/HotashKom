@extends('layouts.yellow.master')

@title('Account Verification Payment')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Account Verification Payment</h5>
                    </div>
                    <div class="card-divider"></div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="payment-details">
                                    <h6>Payment Summary</h6>
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>Verification Fee:</td>
                                                <td class="text-right">৳{{ number_format($verificationFee, 2) }}</td>
                                            </tr>
                                            @if($appliedCoupon)
                                                <tr class="text-success">
                                                    <td>Coupon Discount ({{ $appliedCoupon->code }}):</td>
                                                    <td class="text-right">-৳{{ number_format($discountAmount, 2) }}</td>
                                                </tr>
                                            @endif
                                            <tr class="border-top">
                                                <td><strong>Total Amount:</strong></td>
                                                <td class="text-right"><strong>৳{{ number_format($finalAmount, 2) }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Coupon Application -->
                                    @if(!$appliedCoupon)
                                        <div class="coupon-section mt-4">
                                            <h6>Have a Coupon?</h6>
                                            <form action="{{ route('user.payment.apply-coupon') }}" method="POST" class="form-inline">
                                                @csrf
                                                <div class="input-group w-100">
                                                    <input type="text" name="coupon_code" class="form-control"
                                                           placeholder="Enter coupon code" required>
                                                    <div class="input-group-append">
                                                        <button type="submit" class="btn btn-outline-primary">Apply</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <div class="alert alert-success">
                                            <strong>Coupon Applied:</strong> {{ $appliedCoupon->code }}
                                            ({{ $appliedCoupon->name }})
                                            <a href="{{ route('user.payment.verification') }}" class="float-right text-danger">
                                                Remove
                                            </a>
                                        </div>
                                    @endif

                                    <!-- Payment Button -->
                                    <div class="payment-button mt-4">
                                        <form action="{{ route('user.payment.create') }}" method="POST" id="payment-form">
                                            @csrf
                                            @if($appliedCoupon)
                                                <input type="hidden" name="coupon_code" value="{{ $appliedCoupon->code }}">
                                            @endif
                                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="pay-button">
                                                <i class="fa fa-credit-card"></i> Pay with bKash - ৳{{ number_format($finalAmount, 2) }}
                                            </button>
                                        </form>
                                    </div>

                                    <div class="payment-status mt-3" id="payment-status" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="fa fa-spinner fa-spin"></i> Processing payment...
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="verification-benefits">
                                    <h6>Verification Benefits</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fa fa-check text-success"></i> Access to reseller features</li>
                                        <li><i class="fa fa-check text-success"></i> Custom domain support</li>
                                        <li><i class="fa fa-check text-success"></i> Priority customer support</li>
                                        <li><i class="fa fa-check text-success"></i> Advanced analytics</li>
                                        <li><i class="fa fa-check text-success"></i> Bulk order management</li>
                                    </ul>
                                </div>

                                <div class="payment-instructions mt-4">
                                    <h6>Payment Instructions</h6>
                                    <ol class="small">
                                        <li>Click "Pay with bKash" button</li>
                                        <li>You'll be redirected to bKash payment page</li>
                                        <li>Complete the payment using your bKash account</li>
                                        <li>After successful payment, you'll be redirected back</li>
                                        <li>Your account will be automatically verified</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show loading when form is submitted
    $('#payment-form').submit(function() {
        $('#pay-button').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating Payment...');
        $('#payment-status').show();
    });
});
</script>
@endpush
