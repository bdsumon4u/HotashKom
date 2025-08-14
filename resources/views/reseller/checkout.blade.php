@extends('layouts.reseller.master')

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
@section('breadcrumb-title')
    <h3>Checkout</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">
        <a href="{{ route('reseller.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('reseller.products') }}">Products</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('reseller.checkout') }}">Cart</a>
    </li>
    <li class="breadcrumb-item active">Checkout</li>
@endsection

@section('content')
    @livewire('reseller-checkout')
@endsection
