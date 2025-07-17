@extends('layouts.light.master')

@section('title', 'Add Purchase')

@section('breadcrumb-title')
<h3>Purchases</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Add Purchase</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="mb-5 row">
        <div class="col-sm-12">
            <div class="rounded-sm shadow-sm card">
                <div class="p-3 card-header">
                    <h5>Purchase Information</h5>
                </div>
                <div class="p-3 card-body">
                    @livewire('admin.purchase-create')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
