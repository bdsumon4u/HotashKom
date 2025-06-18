@extends('layouts.yellow.master')

@section('title', 'Checkout')

@push('styles')
<style>
    .form-group {
        margin-bottom: 1rem;
    }
    .card-title {
        margin-bottom: 0.75rem;
    }
    .checkout__totals {
        margin-bottom: 10px;
    }
    .input-number .form-control:focus {
        box-shadow: none;
    }
</style>
@endpush

@section('content')
    <div class="block mt-1 checkout">
        <div class="container">
            @if (auth()->user()?->is_verified)
            <x-form checkoutform :action="route('checkout')" method="POST">
                <livewire:checkout />
            </x-form>
            @else
                <div class="alert alert-warning">
                    <h4 class="alert-heading">Account Not Verified!</h4>
                    <p>Your reseller account is not verified yet. To get verified:</p>
                    <ol>
                        <li>Pay 500 tk to bKash number: 01767677777</li>
                        <li>Wait for admin verification</li>
                        <li>For immediate verification, please call us</li>
                    </ol>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Send data to the server before the user leaves
    window.addEventListener("beforeunload", function (event) {
        // Send data using Fetch API (asynchronous)
        navigator.sendBeacon(
            "/save-checkout-progress",
            new Blob([JSON.stringify({
                name: $('#name').val(),
                phone: $('#phone').val(),
                address: $('#address').val(),
            })], { type: 'application/json' })
        );

        // Optional: If you want to use Fetch (uncomment below)
        // fetch("/api/save-checkout-progress", {
        //     method: "POST",
        //     headers: { "Content-Type": "application/json" },
        //     body: JSON.stringify(formData),
        //     keepalive: true, // Ensures request completes before browser unloads
        // });
    });
</script>
<script>
    $(document).ready(function() {
        $('[place-order]').on('click', function (ev) {
            if ($(this).hasClass('disabled')) {
                ev.preventDefault();
            }
            $(this).text('Processing..').css('opacity', 1).addClass('disabled');
        });
    });
</script>
@endpush