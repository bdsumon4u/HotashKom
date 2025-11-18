@extends('layouts.light.master')

@section('title', 'Edit Purchase')

@section('breadcrumb-title')
<h3>Edit Purchase</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item"><a href="{{ route('admin.purchases.index') }}" wire:navigate.hover>Purchases</a></li>
<li class="breadcrumb-item active">Edit Purchase</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Edit Purchase #{{ $purchase->id }}</h5>
                </div>
                <div class="card-body">
                    @livewire('admin.purchase-edit', ['purchase' => $purchase])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
