@extends('layouts.light.master')

@section('title', 'Receive Courier Return')

@section('breadcrumb-title')
    <h3>Receive Courier Return</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.return') }}">Returns</a></li>
    <li class="breadcrumb-item active">Receive Return</li>
@endsection

@section('breadcrumb-right')
    <a href="{{ route('admin.reports.return') }}" class="btn btn-outline-secondary btn-sm mr-2 font-weight-bold">
        <i class="fa fa-arrow-left mr-1"></i> Return Report
    </a>
    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#accountingGuideModal">
        <i class="fa fa-graduation-cap mr-1"></i> Guide
    </button>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-12">
            @livewire('admin.courier-return-create')
        </div>
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection
