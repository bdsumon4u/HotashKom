@extends('layouts.light.master')

@section('title', 'Record Courier Payout / Sales Settlement')

@section('breadcrumb-title')
    <h3>Courier Payout Settlement</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.accounting.transactions.index') }}">Transactions</a></li>
    <li class="breadcrumb-item active">Courier Settlement</li>
@endsection

@section('breadcrumb-right')
    <a href="{{ route('admin.accounting.shipments.create') }}" class="btn btn-outline-primary btn-sm mr-2 font-weight-bold">
        <i class="fa fa-truck mr-1"></i> New Shipment
    </a>
    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#accountingGuideModal">
        <i class="fa fa-graduation-cap mr-1"></i> Guide
    </button>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-12">
            @livewire('admin.courier-payout-create')
        </div>
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection
