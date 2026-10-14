@extends('layouts.light.master')

@section('title', 'Stock Adjustment / Write-Off')

@section('breadcrumb-title')
    <h3>Stock Adjustment (Damage / Gift)</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Accounting</li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.stock') }}">Stock Report</a></li>
    <li class="breadcrumb-item active">Stock Adjustment</li>
@endsection

@section('breadcrumb-right')
    <a href="{{ route('admin.reports.stock') }}" class="btn btn-outline-primary btn-sm mr-2 font-weight-bold">
        <i class="fa fa-boxes mr-1"></i> Stock Report
    </a>
    <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#accountingGuideModal">
        <i class="fa fa-graduation-cap mr-1"></i> Guide
    </button>
@endsection

@section('content')
<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-12">
            @livewire('admin.stock-adjustment-create')
        </div>
    </div>
</div>

@include('admin.accounting.partials.tutorial-modal')
@endsection
