@extends('layouts.light.master')
@section('title', 'Create Landing Page Pro')

@section('breadcrumb-title')
    <h3>Create Landing Page Pro</h3>
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item">Landing Page Pro</li>
    <li class="breadcrumb-item">Create</li>
@endsection

@section('content')
    <div class="row mb-5">
        <div class="col-sm-12">
            <div class="card rounded-0 shadow-sm">
                <div class="p-3 card-header">Add New <strong>Landing Page Pro</strong></div>
                <div class="p-3 card-body">
                    <form action="{{ route('admin.landing-page-pros.store') }}" method="POST">
                        @csrf
                        @include('admin.landing-page-pros.form')
                    </form>

                    @include('admin.landing-page-pros.image-picker-modal')
                </div>
            </div>
        </div>
    </div>
@endsection
