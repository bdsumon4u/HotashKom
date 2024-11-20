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
    <div class="checkout block mt-1">
        <div class="container">
            <x-form checkoutform :action="route('checkout')" method="POST">
                <livewire:checkout />
            </x-form>
        </div>
    </div>
@endsection

@push('scripts')
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