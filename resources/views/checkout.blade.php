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
            <x-form checkoutform :action="route('checkout')" method="POST">
                <livewire:checkout />
            </x-form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Send data to the server before the user leaves
    window.addEventListener("beforeunload", function (event) {
        // Send data using Fetch API (asynchronous)
        navigator.sendBeacon("/api/save-checkout-progress");

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