@extends('layouts.light.master')

@section('title', 'Ship Products to Courier')

@section('breadcrumb-title')
    <h3>Ship Products to Courier</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.accounting.transactions.index') }}">Transactions</a></li>
    <li class="breadcrumb-item active">New Shipment</li>
@endsection

@section('breadcrumb-right')
    <a href="{{ route('admin.accounting.settlements.create') }}" class="btn btn-outline-success btn-sm mr-2 font-weight-bold">
        <i class="fa fa-money mr-1"></i> Courier Payout Settlement
    </a>
    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#accountingGuideModal">
        <i class="fa fa-graduation-cap mr-1"></i> Guide
    </button>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-12">
            @livewire('admin.shipment-create')
        </div>
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection
